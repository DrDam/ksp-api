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
        
    public function __construct($providersData = []) {
        
        $this->partsData = $providersData['Parts']->dump();
        $this->translationsData = $providersData['Translations']->dump();
    }    
    
    protected function getBasicPartInformations($part, $local) {
        $sizes = $this->getSizes($part);
        $output = [];
        $output['id'] = $part['name'];
        $output['name'] = $this->translationsData['strings'][$part['title']][$local];
        $output['tech'] = $part['TechRequired'];
        $output['cost'] = (float) $part['cost'];
        $output['mass'] = ['empty' => (float) $part['mass'], 'full' => (float) $part['mass']];
        $output['stackable'] = [];
        $output['stackable']['top'] = isset($part['node_stack_top']) ? (isset($sizes[0]) ? $sizes[0] : false) : false ;
        $output['stackable']['bottom'] = isset($part['node_stack_bottom']) ? (isset($sizes[1]) ? $sizes[1] : $sizes[0]): false;
        $output['is_radial'] = ($part['attachRules']['Allow Stack'] == 0) ? true : false ;
        return $output;
    }
    
    private function getSizes($part) {
        $output = [];
        $bulkhead = $part['bulkheadProfiles'];
        $exploded = explode(',', $bulkhead);
        foreach($exploded as $item) {
            if(trim($item) == 'srf') continue;
            $output[] = trim($item);
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
}
