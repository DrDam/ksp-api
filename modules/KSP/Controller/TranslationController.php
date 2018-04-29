<?php

namespace KSP;

use Japloora\ControllerBase;
use Japloora\Config;
use KSP\KSPDataLib;

class TranslationController extends ControllerBase
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
                'path' => 'translation',
                'scheme' => [ROUTE_PARAMETER_SCHEME_HTTP, ROUTE_PARAMETER_SCHEME_HTTPS],
                'method' => [ROUTE_PARAMETER_METHOD_GET],
                'callback' => 'getHome',
            ),
            array(
                'path' => 'locals',
                'scheme' => [ROUTE_PARAMETER_SCHEME_HTTP, ROUTE_PARAMETER_SCHEME_HTTPS],
                'method' => [ROUTE_PARAMETER_METHOD_GET],
                'callback' => 'getLocals',
            ),
            array(
                'path' => 'translation/*',
                'scheme' => [ROUTE_PARAMETER_SCHEME_HTTP, ROUTE_PARAMETER_SCHEME_HTTPS],
                'method' => [ROUTE_PARAMETER_METHOD_GET],
                'callback' => 'getTranslation',
                'parameters' => [
                     'locale' => [
                        'mandatory' => ROUTE_PARAMETER_OPTIONAL,
                        'type' => ROUTE_PARAMETER_TYPE_STRING
                     ],
                ]
            ),
            array(
                'path' => 'translations',
                'scheme' => [ROUTE_PARAMETER_SCHEME_HTTP, ROUTE_PARAMETER_SCHEME_HTTPS],
                'method' => [ROUTE_PARAMETER_METHOD_GET],
                'callback' => 'getTranslations',
                'parameters' => [
                     'keys' => [
                        'mandatory' => ROUTE_PARAMETER_REQUIRED,
                        'type' => ROUTE_PARAMETER_TYPE_STRING
                     ],
                     'locale' => [
                        'mandatory' => ROUTE_PARAMETER_OPTIONAL,
                        'type' => ROUTE_PARAMETER_TYPE_STRING
                     ]
                ]
            ),
        );
    }
    
    public function getHome()
    {
        $info = [];
        
        $callback0 = [];
        $callback0['title'] = 'Get all knowed locals';
        $callback0['description'][] = 'Get list of all locals parsed in dictionnary files';
        $info['locals'] = $callback0;
        
        $callback1 = [];
        $callback1['title'] = 'Get a translated string';
        $callback1['description'][] = 'Return the langcode and the string translated.';
        $callback1['description'][] = 'Output format : { langcode : en-us (default) , transaltions : { id : translated } }';
        $callback1['description'][] = 'Note : the translation key must send without "#" prefix';
        $callback1['option']['local'] = 'You can choose which local use ( see /locals callback)';
        $callback1['exemples'][] = '/translation/autoLOC_18284 : Get translation of #autoLOC_18284 chain.';
        $callback1['exemples'][] = '/translation/autoLOC_18284?local=fr-fr : Get translation of #autoLOC_18284 chain in french.';
        $info['translation/*'] = $callback1;
        
        $callback2 = [];
        $callback2['title'] = 'Get multiple translation strings';
        $callback2['description'][] = 'Return the langcode and  strings translated.';
        $callback2['description'][] = 'Output format : { langcode : en-us (default) , transaltions : { id : translated, id2 : translated2 } }';
        $callback2['option']['local'] = 'You can choose which local use ( see /locals callback)';
        $callback2['option']['keys'] = 'list of translation keys, without "#" prefix, ";" separated';
        $callback2['exemples'][] = '/translations?keys=autoLOC_18284;autoLOC_18285 : Get translation of #autoLOC_18284 and #autoLOC_18285 chains.';
        $callback2['exemples'][] = '/translation?keys=autoLOC_18284?local=fr-fr : Get translation of #autoLOC_18284 chain in french.';
        $info['translations'] = $callback2;
        
        return ['datas'=>$info];
    }
    
    public function getLocals() {
        $locals = $this->lib->getProvider('Translations')->getLocals();
        return ['datas'=> $locals];
    }
    
    public function getTranslation()
    {
        $query = $this->parameters['queryFragments'];
        $translation = $query[0];
        
        $results = $this->getTranslationFromRequest([$translation]);
        
        if(count($results) == 0) {
            return ['datas'=>['no_result' => '']];
        }

        return ['datas'=> $results];
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
