<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace KSP;

use Japloora\Base;
/*
 * Description of KSPDataLib
 *
 * @author drdam
 */
class KSPDataLib {
    
    private $data_provider = [];
    
    public function __construct($do_reset = false) {
        
        Base::discoverClasses('Lib');
        $libs = Base::getImplementation('KSP\KSPDataInterface');
        foreach($libs as $class) {
            $provider_name = $class::getProviderName();
            $provider = new $class($do_reset);

            $this->data_provider[$provider_name] = $provider;
        }        
    }
    
    public function dump() {
        $output = [];
        foreach($this->data_provider as $provider_name => $provider ) {
            $output[$provider_name] = $provider->dump();
        }
        return $output;
    }
    
    public function listProvider() {
        return array_keys($this->data_provider);
    }
    
    public function getProvider($provider_name){
        return $this->data_provider[$provider_name];
    }

}
