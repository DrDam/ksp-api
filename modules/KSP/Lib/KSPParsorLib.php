<?php

namespace KSP;

use KSP\KSPProcessLib;

/**
 * Description of KSPParsorLib
 *
 * @author drdam
 */
class KSPParsorLib
{

    private static $instance = null;
    private $dir = './GameData';
    private $processor = null;
    private $partData = [];
    
    private function __construct()
    {
        $this->processor = new KSPProcessLib();
    }

    public static function create()
    {
        if (self::$instance === null) {
            self::$instance = new KSPParsorLib();
        }
        return self::$instance;
    }

    public function parse()
    {
        $this->navigate($this->dir);
        return $this->partData;
    }

    private function navigate($dir)
    {
        $directory = $this->scanDir($dir);
        foreach ($directory as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->navigate($path);
            } else {
                if (substr(trim($path), -3, 3) == 'cfg' && strstr($dir, 'Parts') != false) {
                    $dirdata = explode('/', $dir);
                    $partData = $this->extract($path);
                    if ($partData == null) {
                        continue;
                    }
                    $provider_tag = $this->getProvider($dirdata);             
                    $partData['category'] = isset($partData['category']) ? $partData['category'] : 'none' ;
                    $partData['provider'] = $provider_tag;
                    $this->partData[$partData['name']] = $partData;
                } else {
                    continue;
                }
            }
        }
    }

    private function getProvider($dirData)
    {
                
        $partKeyId = array_search('Parts', $dirData);
        
        $provider = [];
        
        // $dirData = [. , "GameData" , MOD , [.some.Folders.] , "Parts" , CAT , [.some.Folders.] ];
        for ($i = 2; $i < $partKeyId; $i++) {
            $provider[] = $dirData[$i];
        }
        
        $provider_tag = implode('/', $provider);

        return $provider_tag;
    }
    
    private function scanDir($dir)
    {
        $directory = scandir($dir);
        foreach ($directory as $key => $file) {
            if ($file == '.' || $file == '..') {
                unset($directory[$key]);
            }
        }
        return $directory;
    }

    private function extract($path)
    {
        $file = file($path);

        $nb_line = 0;
        $first_line = preg_replace('/[^A-Za-z0-9 _\-\+\&]/', '', trim($file[0]));
        if ($first_line != 'PART') {
            return null;
        }
        $data = $this->dive($file, $nb_line);
        return array_pop($data);
    }

    private function dive($file, &$nb_line)
    {
        $data = array();
        $max = count($file);
        while ($nb_line < $max) {
            $line = $file[$nb_line];
            $ligne = trim($line);
            if ($ligne == '' || substr($ligne, 0, 1) == '/') {
                $nb_line++;
                continue;
            }

            if (substr($ligne, 0, 1) == '{') {
                $nb_line++;
                continue;
            }

            if (substr($ligne, 0, 1) == '}') {
                $nb_line++;
                return $data;
            }

            if (strstr($line, '=')) {
                $elem = explode('=', $ligne);
                //var_dump($elem);
                $key = trim($elem[0]);
                if (count($elem) - 1 == 1) {
                    $value = trim($elem[1]);
                } else {
                    array_shift($elem);
                    $value = implode(' --- ', $elem);
                }

                $this->putData($data, $key, $value);

                $nb_line++;
                continue;
            } else {
                $type_sanitize = trim(str_replace('{', '', trim($line)));
                $this->master = $type_sanitize;
                $nb_line ++;
                $sub = $this->dive($file, $nb_line);
                $sub['item'] = $type_sanitize;
                $this->putData($data, $type_sanitize, $sub);
                continue;
            }
            $nb_line++;
        }
        return $data;
    }

    private function putData(&$data, $key, $value)
    {

        if (isset($value['name'])) {
            $key = $value['name'];
        }
        if (isset($value['item'])) {
            if ($value['item'] == 'RESOURCE') {
                $key = 'RESSOURCE';
            }
            unset($value['item']);
        }


        $value = ($this->kspprocess($key, $value)) ? $this->kspprocess($key, $value) : $value;

        if (isset($data[$key])) {
            // if $data[$key] is a string
            // => transform it to an array
            if (!is_array($data[$key])) {
                $array = array($data[$key], $value);
                $data[$key] = $array;
            } // if $data[$key] is an of element,
            // not a list => transform to a list
            elseif (!isset($data[$key][0])) {
                $array = array($data[$key]);
                $data[$key] = $array;
                $data[$key][] = $value;
            } // it's allready a list
            else {
                $data[$key][] = $value;
            }
        } else {
            $data[$key] = $value;
        }
    }

    private function kspprocess($key, $value)
    {
        if (is_array($value)) {
            return $value;
        }
        
        $old = '';
        if ($key == 'key') {
            $old = $key;
            $key = $this->master;
        }

        $processors = $this->processor->getProcessors();
        if (in_array($key, array_keys($processors))) {
            $out = $this->processor->{$processors[$key]}($value);
            return $out;
        };
    }
}
