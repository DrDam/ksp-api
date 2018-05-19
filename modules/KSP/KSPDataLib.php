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
        $this->contructProviders($do_reset, $libs);    
    }
    
    private function contructProviders($do_reset = false, $providers) {
        $delayed = [];
        foreach($providers as $class) {
            $dependencies = $class::getDepedencies(); 
            if(!$this->checkDependencies($dependencies)) {
                $delayed[] = $class;
                continue;
            }
            $provider_name = $class::getProviderName();
            $provider = new $class($do_reset, $this->data_provider);

            $this->data_provider[$provider_name] = $provider;
        }
        if(count($delayed) > 0) {
            $this->contructProviders($do_reset, $delayed);
        }
    }
    
    private function checkDependencies($dependencies) {

        foreach($dependencies as $dependencie) {
            if(!in_array($dependencie, array_keys($this->data_provider))) {
                return false;
            }
        }
        return true;
        
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
