<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace KSP;

/**
 * Description of MakeCollections
 *
 * @author drdam
 */
class BaseCollections {
    
    protected $partsData = [];
    protected $translationsData = [];
    protected $fuels = [
        'LiquidFuel' => 5/1000,
        'Oxidizer' => 5/1000,
        'MonoPropellant' => 4/1000,
        'XenonGas' => 1/10000,    
        'SolidFuel' => 75/10000,
        'IntakeAir' => 1,
        'Ore' => 0,
    ];
    protected $G0 = 9.81;
        
    static public function getCollectionName() {
        return 'base';
    }
    
    public function __construct($providersData = []) {
        
        $this->partsData = $providersData['Parts']->dump();
        $this->translationsData = $providersData['Translations']->dump();
    }    
    
    protected function getBasicPartInformations($part, $local) {
        
        $stack_top = $this->getStackItem($part, 'top');
        $stack_bottom = $this->getStackItem($part, 'bottom');

        $sizes = $this->getSizes($part);
        
        $top_size = false;
        if($stack_top != NULL) {
            if(isset($part[$stack_top]['Size'])) {
                $top_size = $part[$stack_top]['Size'];
            }
            else {
                $top_size = $sizes[0];
            }
        }
        
        $bottom_size = false;
        if($stack_bottom != NULL) {
            if(isset($part[$stack_bottom]['Size'])) {
                $bottom_size = $part[$stack_bottom]['Size'];
            }
            else {
                if(count($sizes) == 1){
                    $bottom_size = $sizes[0];
                }
                else {
                    $bottom_size = $sizes[1];
                }
            }
        }
        
        $output = [];
        $output['id'] = $part['name'];
        $output['name'] = $this->translationsData['strings'][$part['title']][$local];
        $output['tech'] = $part['TechRequired'];
        $output['cost'] = (float) $part['cost'];
        $output['mass'] = ['empty' => (float) $part['mass'], 'full' => (float) $part['mass']];
        $output['stackable'] = [];
        $output['stackable']['top'] = $top_size;
        $output['stackable']['bottom'] = $bottom_size;
        $output['is_radial'] = ($part['attachRules']['Allow Stack'] == 0) ? true : false ;
        $output['provider'] = $part['provider'];
        return $output;
    }
    
    protected function getSizes($part) {
        $output = [];
        if(!isset($part['bulkheadProfiles'])) {
            return [];
        }
        
        $bulkhead = $part['bulkheadProfiles'];
        // Manage case of ModuleService add in making history
        if(is_array($bulkhead)) {
            return str_replace('size', '', [$bulkhead[0]]);
        }
        
        $exploded = explode(',', $bulkhead);
        foreach($exploded as $item) {
            if(trim($item) == 'srf') continue;
            $output[] = trim(str_replace('size', '',$item));
        }
        return $output;
    }
    
    protected function addRessourceMass($part) {
        $addMass = 0;
                        $ressources = $part['RESSOURCE'];
                if(isset($ressources[0])) {
                    foreach($ressources as $ressource) {
                        $addMass += $this->fuels[$ressource['name']] * $ressource['amount'];
                    }
                }
                else {
                    $addMass += $this->fuels[$ressources['name']] * $ressources['amount'];
                }
                
                return $addMass;
    }
    
    protected function getStackItem($part, $direction = 'top') {
        $target = 'node_stack_'.$direction;
        
        // If simple exist
        if(isset($part[$target])) {
            return $target;
        }
        
        foreach(array_keys($part) as $key){

            if(strstr($key, $target)) {
                return $key;
            }
        }
        
        return NULL;
    }
}
