<?php

namespace KSP;

use KSP\MakeCollections;
use KSP\KSPDataLibBase;
use KSP\KSPDataInterface;
/**
 * Description of KSPData
 *
 * @author drdam
 */
class CollectionsLib  extends KSPDataLibBase implements KSPDataInterface 
{
    public static function getProviderName() {
        return 'Collections';
    }
    
    public static function getDepedencies() {
        return ['Parts','Translations'];
    }
    
    public function __construct($do_reset = false, $dataProvides = []) {
        $this->dataFile = self::getProviderName();
        parent::__construct($do_reset, $dataProvides);
    }
    
    protected function makeData($dataProvides = [])
    {
        $worker = new MakeCollections($dataProvides);
        $this->data = $worker->make();
        //die();
    }
    
    public function getCollections() {
        return array_keys($this->data);
    }
   
    public function getCollection($name) {
        
        return (isset($this->data[$name])) ? $this->data[$name] : [];
    }

}
