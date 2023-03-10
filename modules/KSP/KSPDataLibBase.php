<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace KSP;

use Japloora\Base;
/*
 * Description of KSPDataLib
 *
 * @author drdam
 */
abstract class KSPDataLibBase {
    protected $data = [];
    protected $dataPath = './DATABASE';
    protected $dataFile = '';
    
    public function __construct($do_reset = false, $dataProvides = [])
    {        
        if ($do_reset === true) {
            unset($this->data);
            $this->createDb($dataProvides);
        } else {
            $this->load();
        }
    }
    
    public static function getDepedencies() {
        return [];
    }
    
    public function dump($sub = '')
    {
        if(!isset($this->data['sub'])) {
            return $this->data;
        }
        return $this->data['sub'];
    }
    
    private function createDb($dataProvides = []) {
        $this->makeData($dataProvides);
        $this->saveData();
    }
    
    abstract protected function makeData($dataProvides = []);
        
    private function saveData()
    {
        if (!is_dir($this->dataPath)) {
            mkdir($this->dataPath);
        }
        if (!file_exists($this->dataPath . '/' . $this->dataFile)) {
            touch($this->dataPath . '/' . $this->getProviderName());
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
}
