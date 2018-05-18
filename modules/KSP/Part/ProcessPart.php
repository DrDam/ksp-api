<?php

namespace KSP;
use KSP\ProcessKeyPart;

class ProcessPart
{
    private $modules = [];
    private $processor = NULL;
    
    public function __construct() {
        $this->processor = new ProcessKeyPart();
    }
    
    public function extract($path)
    {
        unset($this->modules);
        $this->modules = [];
        $file = file($path);

        $nb_line = 0;
        $first_line = preg_replace('/[^A-Za-z0-9 _\-\+\&]/', '', trim($file[0]));
        if ($first_line != 'PART') {
            return null;
        }

        $data = $this->dive($file, $nb_line);
        return ['part' => array_pop($data), 'modules' => array_unique($this->modules)];
    }

    private function dive($file, &$nb_line, $upper_key = '')
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

            // If line containe data
            if (strstr($ligne, '=')) {
                
                // Delete comments
                if(strstr($ligne, '//')) {
                    $exploded = explode('//', $ligne);
                    $ligne = $exploded[0];
                }
                
                $elem = explode('=', $ligne);
                $key = trim($elem[0]);
                $value = trim($elem[1]);
                $this->putData($data, $key, $value, $upper_key);

                $nb_line++;
                continue;
            } else {
                $type_sanitize = trim(str_replace('{', '', trim($line)));
                $upper_key = $type_sanitize;
                $nb_line ++;
                $sub = $this->dive($file, $nb_line, $upper_key);
                $sub['item'] = $type_sanitize;
                $this->putData($data, $type_sanitize, $sub, $upper_key);
                continue;
            }
            
            $nb_line++;
        }
        return $data;
    }

    private function putData(&$data, $key, $value, $upper_key = '')
    {
        if (isset($value['name'])) {
            $key = $value['name'];
            if($upper_key == 'MODULE') {
                $this->modules[] = $key;
            }
        }
        if (isset($value['item'])) {
            if ($value['item'] == 'RESOURCE') {
                $key = 'RESSOURCE';
                $this->modules[] = $key;
            }
            unset($value['item']);
        }

        $value = ($this->kspprocess($key, $value, $upper_key)) ? $this->kspprocess($key, $value, $upper_key) : $value;

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

    private function kspprocess($key, $value, $upper_key)
    {
        if (is_array($value)) {
            return $value;
        }
        
        $old = '';
        if ($key == 'key') {
            $old = $key;
            $key = $upper_key;
        }

        $processors = $this->processor->getProcessors();
        if (in_array($key, array_keys($processors))) {
            $out = $this->processor->{$processors[$key]}($value);
            return $out;
        };
    }
}
