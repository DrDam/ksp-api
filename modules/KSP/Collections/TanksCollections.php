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
class TanksCollections extends BaseCollections {
    
    private $collection = [];

    static public function getCollectionName() {
        return 'fuelTanks';
    }
    
    public function __construct($providersData = []) {
        parent::__construct($providersData);
    }
    
    public function make() {
        $this->makeTankCollection();
        return $this->collection;
    }
    
    private function makeTankCollection() {  
        $collection = [];
        $local = $this->translationsData['locals'][0];
        $parts = $this->partsData['parts'];
        foreach($parts as $part_id => $part) {
            if($part['category'] != 'FuelTank' && $part['category'] != 'Propulsion') continue;
            if($part_id == 'fuelLine') continue;
            if(!isset($part['RESSOURCE'])) continue;
            
            
            $fuelTank = $this->getBasicPartInformations($part, $local);
   
            // Add Fuel Mass
            $addMass = $this->addRessourceMass($part);
            $fuelTank['mass']['full'] += $addMass;
            
            // Add Ressources
             $ressources = $part['RESSOURCE'];
            if(isset($ressources[0])) {
                foreach($ressources as $ressource) {
                    $fuelTank['ressources'][$ressource['name']] = $ressource['amount'];
                }
            }
            else {
                $fuelTank['ressources'][$ressources['name']] = $ressources['amount'];
            }
            
            $collection[$part_id] = $fuelTank;
        }
        $this->collection = $collection;
    }
}
