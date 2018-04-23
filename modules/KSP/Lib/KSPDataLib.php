<?php

namespace KSP;

use KSP\KSPParsorLib;

/**
 * Description of KSPData
 *
 * @author drdam
 */
class KSPDataLib
{
    private $data = [];
    private $dataPath = './partData';
    private $dataFile = 'Datas';
    
    public function __construct($do_reset = false)
    {
        if ($do_reset === true) {
            unset($this->data);
            $this->createDb();
        } else {
            $this->load();
        }
    }
    
    public function dump()
    {
        return $this->data;
    }
    
    private function createDb() {
        $this->makeData();
        $this->saveData();
    }
    
    private function makeData()
    {
        $parsor = KSPParsorLib::create();
        $this->data = $parsor->parse();
    }
    
    private function saveData()
    {
        if (!is_dir($this->dataPath)) {
            mkdir($this->dataPath);
        }
        if (!file_exists($this->dataPath . '/' . $this->dataFile)) {
            touch($this->dataPath . '/' . $this->dataFile);
        }
        file_put_contents($this->dataPath . '/' . $this->dataFile, serialize($this->data));
    }
    
    private function load()
    {
        $file_path = $this->dataPath . '/' . $this->dataFile;
        if(!file_exists($file_path)) {
            $this->createDb();
        }
        $file_data = file_get_contents($file_path);
        $this->data = unserialize($file_data);
    }
    
    public function getParts($parts = [], $keys = NULL) {
        $out = [];
        
        foreach ($parts as $part) {
            if(isset($this->data[$part])) {
                $part_item = $this->data[$part];

                if(is_array($keys)) {
                    $item = [];
                    foreach($keys as $key_item) {
                        if(isset($part_item[$key_item])) {
                            $item[$key_item] = $part_item[$key_item];
                        }
                    }
                    $part_item = $item;
                }
                
                $out[$part] = $part_item;
            }
        }
        
        return $out;
    }
}
