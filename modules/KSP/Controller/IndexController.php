<?php

namespace KSP;

use Japloora\ControllerBase;
use Japloora\Config;
use KSP\SearchPart;

class IndexController extends ControllerBase
{
    private $searcher;

    public function __construct()
    {
        $this->searcher = new SearchPart();
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
        $callback1['title'] = 'List all data of an index';
        $callback1['description'][] = 'Return an index from used key ( see below the list of available keys - casse sensitiv)';
        $callback1['option']['with_parts'] = 'Choose if you want directly the list of parts form each index items';
        $callback1['exemples'][] = '/index/category : Return all "category" and the number of part inside.';
        $callback1['exemples'][] = '/index/TechRequired?with_parts : Return all "TechRequired" items and the list of parts inside.';
        $clefs = [];
        $callback1['keys'] = $this->searcher->getClefs();
        $info['index/*'] = $callback1;
        
        $callback2 = [];
        $callback2['title'] = 'See list of parts inside a index item';
        $callback2['description'][] = 'Return the list of part attached to a index item (see \'/index/*\' callback)';
        $callback2['exemples'][] = '/index/category/Control : Return the list of part attached to a index \'category\'  and item \'Control\'.';
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
