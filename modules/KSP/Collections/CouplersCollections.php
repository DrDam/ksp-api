<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace KSP;

use KSP\BaseCollections;

/**
 * Description of MakeCollections
 *
 * @author drdam
 */
class CouplersCollections extends BaseCollections {
    
    private $collection = [];

    static public function getCollectionName() {
        return 'couplers';
    }
    
    public function __construct($providersData = []) {
        parent::__construct($providersData);
    }
    
    public function make() {
        $this->makeCouplersCollection();
        return $this->collection;
    }
    
    private function makeCouplersCollection() {  
        $collection = [];
        $local = $this->translationsData['locals'][0];
        $parts = $this->partsData['parts'];
        foreach($parts as $part_id => $part) {
           
            if(!$this->isCoupler($part)) {
                continue;
            }
            
            if(isset($part['ModuleCommand'])) continue;
            if($part['fuelCrossFeed'] == 'False') continue;
            // disable Mk3 Engine Mount
            if($part_id == 'adapterEngines') continue;
            
            $coupler = $this->getBasicPartInformations($part, $local);
            $coupler['stackable']['bottom_number'] =  $this->count_stack($part, 'bottom');
            $collection[$part_id] = $coupler;
        }
        $this->collection = $collection;
    }
    
    private function count_stack($part, $position) {
        $target = 'node_stack_'.$position ;
        $count = 0;
        foreach(array_keys($part) as $key){

            if(strstr($key, $target)) {
                $count++;
            }
        }
        
        return $count;
    }
    
    private function isCoupler($part) {
        $nbTop = $this->count_stack($part, 'top');
        $nbBottom = $this->count_stack($part, 'bottom');
        
        if($nbTop >= 1 && $nbBottom >= 1 && $nbTop != $nbBottom && $part['attachRules']['Stack'] == 1) {
            return TRUE;
        }
        return FALSE;
    }
}
