<?php

namespace KSP;

/**
 * Description of KSPParsorLib
 *
 * @author drdam
 */
class ParsorTranslations
{
    private $dir = './GameData';
    
    public function parse()
    {
        return $this->navigate($this->dir);
    }

    private function navigate($dir)
    {
        $locals = [];
        $strings = [];
        $directory = $this->scanDir($dir);
        foreach ($directory as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $dive = $this->navigate($path);
                $locals = array_merge($locals, $dive['locals']);                
                $strings = array_merge($strings, $dive['strings']);                
            } else {
                if($file == 'dictionary.cfg') {
                    $translations = $this->extract($path);
                    $locals = array_merge($locals, $translations['locals']);                
                    $strings = array_merge($strings, $translations['strings']);   
                }
                else {
                    continue;
                }
            }
        }

        $output['locals'] = array_unique($locals);
        $output['strings'] = $strings;
        return $output;
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
        $locals = [];
        $strings = [];
        $file = file($path);
        // start on line 3 ( ﻿Localization + {
        
        $nb_line = 2;
        $max = count($file);
        $version = 'en-us';
        while ($nb_line < $max) {
            $line = trim($file[$nb_line]);

            if($line == '{' || $line == '}') {

            }
            else {
                if(!strstr($line, '=')) {
                $version = $line;
                $locals[] = $version;
                }
                else {
                    $data = explode('=', $line);
                    $key = trim($data[0]);
                    $trans = trim($data[1]);
                    $strings[$key][$version] = $trans;

                }
            }
            $nb_line++;
        }
        $output['locals'] = $locals;
        $output['strings'] = $strings;
        return $output;
    }

}
