<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace KSP\Collections;

use KSP\Collections\EngineCollections;
use Japloora\Base;
/**
 * Description of MakeCollections
 *
 * @author drdam
 */
class MakeCollections {

    private $collections = [];
    private $providers = [];

    public function __construct($providersData = []) {

        Base::discoverClasses('Collections');
        $providersClasses = Base::getExtends('BaseCollections', 'KSP');
        foreach($providersClasses as $class) {
            $this->providers[$class::getCollectionName()] = new $class($providersData);
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
