<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace KSP\Collections;

use KSP\Collections\BaseCollections;

/**
 * Description of MakeCollections
 *
 * @author drdam
 */
class AdaptersCollections extends BaseCollections {

    private $collection = [];

    static public function getCollectionName() {
        return 'adapters';
    }

    public function __construct($providersData = []) {
        parent::__construct($providersData);
    }

    public function make() {
        $this->makeAdaptersCollection();
        return $this->collection;
    }

    private function makeAdaptersCollection() {
        $collection = [];
        $local = $this->translationsData['locals'][0];
        $parts = $this->partsData['parts'];
        foreach($parts as $part_id => $part) {

            $sizes = $this->getSizes($part);
            if(count($sizes) != 2) {
                continue;
            }

            if(isset($part['ModuleCommand'])) continue;
            if($part['category'] == 'FuelTank') continue;
            if(isset($part['RESSOURCE'])) continue;
            if($part['category'] == 'Coupling') continue;
            // Get out X-couplers
            if($this->count_bottom($part) > 1) continue;

            $adapter = $this->getBasicPartInformations($part, $local);

            $collection[$part_id] = $adapter;
        }
        $this->collection = $collection;
    }

    private function count_bottom($part) {
        $target = 'node_stack_bottom';
        $count = 0;
        foreach(array_keys($part) as $key){

            if(strstr($key, $target)) {
                $count++;
            }
        }

        return $count;
    }
}
