<?php

namespace KSP;

use Japloora\ControllerBase;
use Japloora\Config;
use KSP\KSPDataLib;

class CollectionsController extends ControllerBase
{
    private $lib;

    public function __construct()
    {
        $this->lib = new KSPDataLib();
    }
    
    public static function defineRoutes()
    {
        return array(
            array(
                'path' => 'collections',
                'scheme' => [ROUTE_PARAMETER_SCHEME_HTTP, ROUTE_PARAMETER_SCHEME_HTTPS],
                'method' => [ROUTE_PARAMETER_METHOD_GET],
                'callback' => 'getHome',
            ),
            array(
                'path' => 'collection/*',
                'scheme' => [ROUTE_PARAMETER_SCHEME_HTTP, ROUTE_PARAMETER_SCHEME_HTTPS],
                'method' => [ROUTE_PARAMETER_METHOD_GET],
                'callback' => 'getCollection',
                'parameters' => [
                     'src' => [
                        'mandatory' => ROUTE_PARAMETER_OPTIONAL,
                        'type' => ROUTE_PARAMETER_TYPE_STRING
                     ],
                ],
            ),
        );
    }
    
    public function getHome()
    {
        $info = [];
        
        $callback0 = [];
        $callback0['title'] = 'Get all created collections';
        $callback0['description'][] = 'Get list of all created collections of parts';
        $callback0['description'][] = 'list of created collections :';
        $callback0['collections'] = $this->getCollections();
        $info['collections'] = $callback0;
        
        $callback1 = [];
        $callback1['title'] = 'Get data of a part collection';
        $callback1['description'][] = 'Return all part inside a collection with a list of selected/computed values.';
        $callback1['description'][] = 'Add provider selector : ?src=[provider]';
        $callback1['providers'] = $this->getProviderList();
        $callback1['exemples'][] = '/collection/engines : Get all engines with selected values.';
        $callback1['exemples'][] = '/collection/engines?src=squad : Get only stock engines.';
        $info['collection/*'] = $callback1;
        
        return ['datas'=>$info];
    }
    
    public function getCollections() {
        return $this->lib->getProvider('Collections')->getCollections();
    }
    
    public function getProviderList() {
        return $this->lib->getProvider('Collections')->getProviderList();
    }
    
    public function getCollection()
    {
        $source = ($this->parameters['Query']['src'] != '') ? $this->parameters['Query']['src'] : '';

        $query = $this->parameters['queryFragments'];
        $collection_name = $query[0];
        
        $results = $this->lib->getProvider('Collections')->getCollection($collection_name, $source);

        if(count($results) == 0) {
            return ['datas'=>['no_result' => '']];
        }

        return ['datas'=> [$collection_name => $results]];
    }
    
    public function getTranslations()
    {
        $translations = [];
        if (isset($this->parameters['Query']['keys'])) {
            $translations_list = $this->parameters['Query']['keys'];
            $translations = explode(';', $translations_list);
        }
             
        $results = $this->getTranslationFromRequest($translations);
        
        if(count($results) == 0) {
            return ['datas'=>['no_result' => '']];
        }

        return ['datas'=> $results];
    }
    
    private function getTranslationFromRequest($ids = []) {
        
        $locale = "en-us";
        $keys = NULL;
        if (isset($this->parameters['Query']['locale'])) {
            $local_key = $this->parameters['Query']['locale'];
            $locales = $this->lib->getProvider('Translations')->getLocals();
            if(!in_array($local_key, $locales['locals'])) {
                // Error
            } else {
                $locale = $local_key;
            }
        }
        
        return $this->lib->getProvider('Translations')->getTranslations($locale, $ids);
        
    }
}
