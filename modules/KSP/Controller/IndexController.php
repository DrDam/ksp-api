<?php

namespace KSP;

use Japloora\ControllerBase;
use Japloora\Config;
use KSP\KSPSearchLib;

class IndexController extends ControllerBase
{
    private $searcher;

    public function __construct()
    {
        $this->searcher = new KSPSearchLib();
    }
    
    public static function defineRoutes()
    {
        return array(
            array(
                'path' => 'index',
                'scheme' => [ROUTE_PARAMETER_SCHEME_HTTP, ROUTE_PARAMETER_SCHEME_HTTPS],
                'method' => [ROUTE_PARAMETER_METHOD_GET],
                'callback' => 'getHome',
            ),
            array(
                'path' => 'index/*',
                'scheme' => [ROUTE_PARAMETER_SCHEME_HTTP, ROUTE_PARAMETER_SCHEME_HTTPS],
                'method' => [ROUTE_PARAMETER_METHOD_GET],
                'callback' => 'getIndex',
                'parameters' => [
                     'with_parts' => [
                        'mandatory' => ROUTE_PARAMETER_OPTIONAL,
                        'type' => ROUTE_PARAMETER_TYPE_STRING
                     ],
                    ]
            ),
            array(
                'path' => 'index/*/*',
                'scheme' => [ROUTE_PARAMETER_SCHEME_HTTP, ROUTE_PARAMETER_SCHEME_HTTPS],
                'method' => [ROUTE_PARAMETER_METHOD_GET],
                'callback' => 'getIndexItem',
            ),
        );
    }
    
    public function getHome()
    {        
        $info = [];
        
        $callback1 = [];
        $callback1['title'] = 'Fabrication d\'index.';
        $callback1['description'] = 'Retourne un index à partir de la clef demandé (voir liste ci-après -- la clef est sensible à la casse)';
        $callback1['option'] = 'Le paramètre \'with_parts\' permet de renvoyer la liste des indentifiant des pieces.';
        $callback1['exemples'][] = '/index/category : retourne la liste des catégories de pièces.';
        $callback1['exemples'][] = '/index/TechRequired?with_parts : retourne la liste des techno nécessaire au dévérouillage des pieces, ainsi que les pièces impactées.';
        $clefs = [];
        $callback1['clefs'] = $this->searcher->getClefs();
        $info['index/*'] = $callback1;
        
        $callback2 = [];
        $callback2['title'] = 'Détail d\'un élement d\'index';
        $callback2['description'] = 'Retourne la liste des identifiants des pièces correspondante à un élément d\'un index (voir \'/index/*\')';
        $callback2['exemples'][] = '/index/category/Control : retourne la liste des pièces dont la catégory est \'Control\'.';
        $info['index/*/*'] = $callback2;
        
        return ['datas'=>$info];
    }

    public function getIndex()
    {
        $query = $this->parameters['queryFragments'];
        $index = $query[0];
        
        $with_parts = FALSE;
        if (isset($this->parameters['Query']['with_parts'])) {
            $with_parts = TRUE;
        }
        $results = $this->searcher->get($index, $with_parts);
        
        if(count($results) == 0) {
            return ['datas'=>['no_result' => 'merci de consulter la liste des clefs disponible à l\'url \'/index\'']];
        }

        return ['datas'=>[$index => $results]];
    }
    
    public function getIndexItem()
    {
        $query = $this->parameters['queryFragments'];
        $index = $query[0];
        $item = $query[1];
        
        $index_data = $this->searcher->get($index, TRUE);
        if(count($index_data) == 0) {
            return ['datas'=>['no_results' => 'merci de consulter la liste des clefs disponible à l\'url \'/index\'']];
        }
        
        if(!isset($index_data[$item])) {
           return ['datas'=>['no_results' => 'merci de consulter la liste des élements disponible à l\'url \'/index/'.$index.'\'']]; 
        }
       else {
           return ['datas'=>[$item => $index_data[$item]]];
       }
    }
}
