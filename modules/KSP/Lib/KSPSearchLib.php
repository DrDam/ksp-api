<?php

namespace KSP;

use KSP\KSPDataLib;

/**
 * Description of KSPSearcherLib
 *
 * @author drdam
 */
class KSPSearchLib {
    
    private $data = [];
    
    public function __construct() {
        $dataLib = new KSPDataLib();
        $this->data = $dataLib->dump();
    }
    
    public function get($key, $with_parts = FALSE) {
        
        $out = [];
        
        foreach($this->data as $part_name => $part_data) {
            if(isset($part_data[$key])) {
                if(!isset($out[$part_data[$key]])) {
                    $out[$part_data[$key]] = [];
                }
                $out[$part_data[$key]]['parts'][] = $part_name;
            }
        }
        
        if($with_parts === FALSE) {
            $out2 = [];
            foreach($out as $key_item => $datas) {
                $count = count($datas['parts']);
                $out2[$key_item] = $count;
            } 
            return $out2;
        }
        else {
            return $out;
        }
    }

    public function getClefs() {
        $clefs = [];
        
        foreach($this->data as $part_name => $part_data) {
            foreach($part_data as $key => $values) {
                if(!is_array($values)) {
                    if(!isset($clefs[$key]));
                    $clefs[$key] = $key;
                }
            }
        }
        
        return array_keys($clefs);
    }
}
