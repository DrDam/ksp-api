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
class DecouplersCollections extends BaseCollections {
    
    private $collection = [];

    static public function getCollectionName() {
        return 'decouplers';
    }
    
    public function __construct($providersData = []) {
        parent::__construct($providersData);
    }
    
    public function make() {
        $this->makeDecouplersCollection();
        return $this->collection;
    }
    
    private function makeDecouplersCollection() {  
        $collection = [];
        $coupleurs_names = $this->partsData['modules']['decouple'];
        $local = $this->translationsData['locals'][0];
        $parts = $this->partsData['parts'];
        foreach($coupleurs_names as $couplers_id) {
            $coupleur = $parts[$couplers_id];
            
            // avoid shields
            if($coupleur['category'] != 'Coupling') {
                continue;
            }
            
            $module = $coupleur['ModuleDecouple'];
            
            // Controle omni découpleur
            $is_omni = (isset($module['isOmniDecoupler']) && $module['isOmniDecoupler'] == true);
            
            // avoid Engine Plates
            if(!$is_omni && $module['explosiveNodeID'] == 'bottom') {
                continue;
            }

            // Basic informations
            $coupleur_data = $this->getBasicPartInformations($coupleur, $local);
            
            // Add OmniDecoupler information
            $coupleur_data['isOmniDecoupler'] = $is_omni;

            $collection[$couplers_id] = $coupleur_data;
        }
        $this->collection = $collection;
    }
}
