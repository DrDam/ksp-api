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
class EngineCollections extends BaseCollections {
    
    private $collection = [];
    
    static public function getCollectionName() {
        return 'engines';
    }
    
    public function __construct($providersData = []) {
        parent::__construct($providersData);
    }
    
    public function make() {
        $this->makeEnginesCollection();
        return $this->collection;
    }
    
    private function makeEnginesCollection() {
        $collection = [];
        $engines_names = $this->partsData['modules']['engines'];
        $local = $this->translationsData['locals'][0];
        $parts = $this->partsData['parts'];
        foreach($engines_names as $engine_id) {
            $engine = $parts[$engine_id];
            
            // Avoid "Size1p5_Tank_05" part
            if($engine['category'] == 'FuelTank') {
                continue;
            }
            
            $engine_data = [];
            
            // Basic informations
            $engine_data = $this->getBasicPartInformations($engine, $local);
            
            // Engine Burning informations
            $ModuleEngine = (isset($engine['ModuleEnginesFX'])) ? $engine['ModuleEnginesFX'] : $engine['ModuleEngines'];
            $data = [];
            if(isset($ModuleEngine[0])) {
                // multimode Engine
                foreach($ModuleEngine as $mode) {
                    $Modedata = $this->extractEngineCaracteristics($mode);
                    $data[$Modedata['type']][] = $Modedata;
                }
            }
            else {
                // monomode Engine
                $Modedata = $this->extractEngineCaracteristics($ModuleEngine);
                $data[$Modedata['type']][] = $Modedata;
            }
            
            // Manage FuelTank-Engine
            if(isset($engine['RESSOURCE'])) {
                $addMass = $this->addRessourceMass($engine);
                $engine_data['mass']['full'] += $addMass;
            }
            
            $engine_data['modes'] = $data;            
            $collection[$engine_id] = $engine_data;
        }
        $this->collection = $collection;
    }
    
    private function extractEngineCaracteristics($engineData) {
        $output = [];
        $type = $engineData['EngineType'];
                
        if($type != 'Turbine') {
            $completeCurve = $this->makeSimpleCurve($engineData);
            $consumptions = $this->getConsumptions($engineData, $completeCurve['conso']);
            $output['MaxThrust'] = (float) $engineData['maxThrust'];
            $output['curve'] = $completeCurve['curve'];
            $output['conso']['proportions'] = $consumptions['fuels'];
            $output['conso']['total']['mass'] = round($completeCurve['conso'],4);
            $output['conso']['total']['unit'] = round($consumptions['unit'],4);
        }
        else {
            $completeCurve = $this->makeJetCurve($engineData);
            $output['ISP_curve'] = $completeCurve['ISP'];
            $output['Thrust_curve'] = $completeCurve['Thrust'];
        }
        $output['type'] = $type;
        return $output;
    }
    
    private function makeSimpleCurve($engineData) {
        $completeCurve = [];
        $consump = null;
        $MaxThrust = (float) $engineData['maxThrust'];
        $curve = $engineData['atmosphereCurve']['key'];

        // calcul of consumption for non atmo situation
        foreach($curve as $point) {
            if($point['Atmospher'] != 0) {
                continue;
            }
            else {
                $consump = $MaxThrust/$point['ISP']/$this->G0;
            }
        }

        // Complete Curve with consumption
        foreach($curve as $point) {
            $data = [];
            $data['atmo'] = (float) $point['Atmospher'];
            $data['ISP'] = (float) $point['ISP'];
            $data['Thrust'] = (float) round($point['ISP'] * $this->G0 * $consump, 4);
            $completeCurve[] = $data;
        }  
        
        return  ['conso' => $consump, 'curve' => $completeCurve];
    }
    
    private function getConsumptions($engineData, $conso) {
        $fuels = [];
        $tot = 0;
        foreach($this->fuels as $type => $mass) {
            if(isset($engineData[$type])) {
                $ratio = (float) $engineData[$type]['ratio'];
                $fuels[$type] = $ratio;
                $tot += $fuels[$type] * $mass;
            }
        }
        
        if(count($fuels) == 0 ) {
            $fuels = 'fuck';
            return ['fuels' => 'fuck', 'unit' => 'fuck'];
        }
        
        return ['fuels' => $fuels, 'unit' => $conso / ($tot / count($fuels))];
    }
    
    private function makeJetCurve($engineData) {
        $ISPCurveOutput = [];
        $ISP = $engineData['atmosphereCurve']['key']['ISP'];
        $ISPcurve = $engineData['atmCurve']['key'];
        foreach($ISPcurve as $point) {
            $data = [];
            $data['atmo'] = (float) $point['Atmospher'];
            $data['ISP'] = (float) round($point['ISP_factor'] * $ISP,4);
            $data['slop_left'] =  (float) $point['slope left'];
            $data['slop_right'] =  (float) $point['slope right'];
            $ISPCurveOutput[] = $data;
        }

        $MaxThrust = (float) $engineData['maxThrust'];
        $ThrustCurve = $engineData['velCurve']['key'];
        foreach($ThrustCurve as $point) {
            $data = [];
            $data['Velocity'] = (float) $point['Velocity'];
            $data['Thrust'] = (float) round($point['thrust_factor'] * $MaxThrust,4);
            $data['slop_left'] =  (float) $point['slope left'];
            $data['slop_right'] =  (float) $point['slope right'];
            $ThrustCurve[] = $data;
        }
        
        return  ['ISP' => $ISPCurveOutput, 'Thrust' => $ThrustCurve];
    }

}
