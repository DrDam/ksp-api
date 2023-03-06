<?php

namespace KSP\Part;

use KSP\PartLib;

/**
 * Description of KSPSearcherLib
 *
 * @author drdam
 */
class SearchPart {

    private $parts = [];
    private $modules = [];

    public function __construct() {
        $dataLib = new PartLib();
        $data = $dataLib->dump();
        $this->parts = $data['parts'];
        $this->modules = $data['modules'];
    }

    public function getByKey($key, $with_parts = FALSE) {

        $out = [];

        foreach($this->parts as $part_name => $part_data) {
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

        foreach($this->parts as $part_name => $part_data) {
            foreach($part_data as $key => $values) {
                if(!is_array($values)) {
                    if(!isset($clefs[$key]));
                    $clefs[$key] = $key;
                }
            }
        }

        return array_keys($clefs);
    }

    public function getModules($with_parts = FALSE) {
        if($with_parts === TRUE) {
            return $this->modules;
        }
        else {
            $out = [];
            foreach($this->modules as $key => $value) {
                $out[$key] = count($value);
            }
            return $out;
        }
    }

    public function getModuleParts($module) {
        if(isset($this->modules[$module])) {
            return $this->modules[$module];
        }
        return NULL;
    }
}
