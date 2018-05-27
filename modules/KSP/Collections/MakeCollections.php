<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace KSP;

use KSP\EngineCollections;
/**
 * Description of MakeCollections
 *
 * @author drdam
 */
class MakeCollections {

    private $collections = [];

    private $providersClass = [
        'engines' => 'KSP\EngineCollections',
        'fuelTanks' => 'KSP\TanksCollections',
        'decouplers' => 'KSP\DecouplersCollections',
    ];
    private $providers = [];
        
    public function __construct($providersData = []) {
        
        foreach($this->providersClass as $type => $class) {
            $this->providers[$type] = new $class($providersData);
        }
    }
    
    public function make() {
        
        foreach($this->providers as $type => $obj) {
            $data = $obj->make();
            $this->collections[$type] = $data;
        }

        return $this->collections;
        
    }
    
}
