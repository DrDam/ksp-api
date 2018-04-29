<?php

namespace KSP;

use KSP\ParsorPart;
use KSP\KSPDataInterface;
use KSP\KSPDataLibBase;
/**
 * Description of KSPData
 *
 * @author drdam
 */
class PartLib extends KSPDataLibBase implements KSPDataInterface 
{
    public static function getProviderName() {
        return 'Parts';
    }
    
    public function __construct($do_reset = false) {
        $this->dataFile = self::getProviderName();
        parent::__construct($do_reset);
    }
    
    protected function makeData()
    {
        $parsor = ParsorPart::create();
        $this->data = $parsor->parse();
    }
    public function getParts($parts = [], $keys = NULL) {
        $out = [];
        
        foreach ($parts as $part) {
            if(isset($this->data[$part])) {
                $part_item = $this->data[$part];

                if(is_array($keys)) {
                    $item = [];
                    foreach($keys as $key_item) {
                        
                        $part_data = $part_item;
                        $key_data = explode('.', $key_item);
                        foreach($key_data as $diver) {
                            if(isset($part_data[$diver])) {
                                $part_data = $part_data[$diver];
                            }
                        }
                        $item[$key_item] = $part_data;

                    }
                    $part_item = $item;
                }
                
                $out[$part] = $part_item;
            }
        }
        
        return $out;
    }
}