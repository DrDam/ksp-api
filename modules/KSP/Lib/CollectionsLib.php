<?php

namespace KSP;

use KSP\Collections\MakeCollections;
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

    public function getCollection($name, $source = '') {

        $collection = (isset($this->data[$name])) ? $this->data[$name] : [];
        if($source == '') {
            return $collection;
        }
        else {
            foreach($collection as $partk_key => $part_data) {
                if(strtolower($part_data['provider']) != strtolower($source)) {
                    unset($collection[$partk_key]);
                }
            }
            return (count($collection) > 0) ? $collection : [];
        }
    }

    public function getProviderList() {
        $provider_list = [];

        foreach($this->data as $collection_name => $collection) {
            foreach($collection as $part_name => $part_data) {
                if(!isset($provider_list[$part_data['provider']])) {
                    $provider_list[$part_data['provider']] = strtolower($part_data['provider']);
                }
            }
        }

        return array_values($provider_list);
    }
}
