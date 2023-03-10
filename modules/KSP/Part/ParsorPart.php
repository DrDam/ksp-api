<?php

namespace KSP\Part;

use KSP\Part\ProcessPart;
use KSP\Part\ModulesPart;

/**
 * Description of KSPParsorLib
 *
 * @author drdam
 */
class ParsorPart
{
    private $dir = './GameData';
    private $processor = null;
    private $moduleProcessor = null;
    private $partData = [];
    private $modules = [];

    public function __construct()
    {
        $this->processor = new ProcessPart();
        $this->moduleProcessor = new ModulesPart();
    }

    public function parse()
    {
        $this->navigate($this->dir);
        return ['parts' => $this->partData, 'modules' => $this->modules];
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
                    $extracted = $this->processor->extract($path);
                    if ($extracted == null) {
                        continue;
                    }
                    $provider_tag = $this->getProvider($dirdata);
                    $partData = $extracted['part'];
                    $partModules = $extracted['modules'];

                    $partData['category'] = isset($partData['category']) ? $partData['category'] : 'none' ;
                    $partData['provider'] = $provider_tag;

                    $this->partData[$partData['name']] = $partData;
                    $this->updateModules($partModules, $partData['name']);
                } else {
                    continue;
                }
            }
        }
    }

    private function updateModules($new_modules = [], $part_name = '') {

        foreach($new_modules as $key) {

            $translated_key = $this->moduleProcessor->getModule($key);

            $this->modules[$translated_key][] = $part_name;
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
}
