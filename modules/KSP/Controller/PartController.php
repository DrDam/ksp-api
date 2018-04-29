<?php

namespace KSP;

use Japloora\ControllerBase;
use Japloora\Config;
use KSP\KSPDataLib;

class PartController extends ControllerBase
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
                'path' => 'part',
                'scheme' => [ROUTE_PARAMETER_SCHEME_HTTP, ROUTE_PARAMETER_SCHEME_HTTPS],
                'method' => [ROUTE_PARAMETER_METHOD_GET],
                'callback' => 'getHome',
            ),
            array(
                'path' => 'part/*',
                'scheme' => [ROUTE_PARAMETER_SCHEME_HTTP, ROUTE_PARAMETER_SCHEME_HTTPS],
                'method' => [ROUTE_PARAMETER_METHOD_GET],
                'callback' => 'getPart',
                'parameters' => [
                     'mode' => [
                        'mandatory' => ROUTE_PARAMETER_OPTIONAL,
                        'type' => ROUTE_PARAMETER_TYPE_STRING
                     ],
                     'key_list' => [
                        'mandatory' => ROUTE_PARAMETER_OPTIONAL,
                        'type' => ROUTE_PARAMETER_TYPE_STRING
                     ],
                ]
            ),
            array(
                'path' => 'parts',
                'scheme' => [ROUTE_PARAMETER_SCHEME_HTTP, ROUTE_PARAMETER_SCHEME_HTTPS],
                'method' => [ROUTE_PARAMETER_METHOD_GET],
                'callback' => 'getParts',
                'parameters' => [
                     'part_name' => [
                        'mandatory' => ROUTE_PARAMETER_REQUIRED,
                        'type' => ROUTE_PARAMETER_TYPE_STRING
                     ],
                     'mode' => [
                        'mandatory' => ROUTE_PARAMETER_OPTIONAL,
                        'type' => ROUTE_PARAMETER_TYPE_STRING
                     ],
                     'key_list' => [
                        'mandatory' => ROUTE_PARAMETER_OPTIONAL,
                        'type' => ROUTE_PARAMETER_TYPE_STRING
                     ],
                ]
            ),
        );
    }
    
    public function getHome()
    {
        $info = [];
        
        $callback1 = [];
        $callback1['title'] = 'Get part data';
        $callback1['description'][] = 'Return all informations knowed from a part ( part name are case sensitiv)';
        $callback1['description'][] = 'Translated values "autoLoc_XXXXX" formed, need to be request with "translations" callbacks';
        $callback1['option']['mode'][] = 'The type of output ( light par défaut, full, custom) defining which field are returned.';
        $callback1['option']['mode'][] = 'the default list ( mode = "light" ) are : name,provider,title,description,author,category,subcategory,mass.';
        $callback1['option']['key_list'][] = 'The \'key_list\' parameter ( mandatory in "custom" mode) list all field returned, separated by ";".';        
        $callback1['option']['key_list'][] = 'Note : You can select a sub key with "key.subkey" syntax';        
        $callback1['exemples'][] = '/part/sasModule : Return light collection of data for sasModule part.';
        $callback1['exemples'][] = '/part/sasModule?mode=custom&key_list=title;author : Return only "title" and "author" information for sasModule part.';
        $info['part/*'] = $callback1;
        
        $callback2 = [];
        $callback2['title'] = 'Get data for multiple parts';
        $callback2['description'][] = 'Return all informations knowed from the differents parts ( part name are case sensitiv)';
        $callback2['description'][] = 'Translated values "autoLoc_XXXXX" formed, need to be request with "translations" callbacks';
        $callback2['option']['part_name'] = 'This parameter contain list of part name separated by a \';\'.';
        $callback2['option']['mode'][] = 'The type of output ( light par défaut, full, custom) defining which field are returned.';
        $callback2['option']['mode'][] = 'the default list ( mode = "light" ) are : name,provider,title,description,author,category,subcategory,mass.';
        $callback2['option']['key_list'][] = 'The \'key_list\' parameter ( mandatory in "custom" mode) list all field returned, separated by ";".';        
        $callback2['option']['key_list'][] = 'Note : You can select a sub key with "key.subkey" syntax';        
        $callback2['exemples'][] = '/parts?part_name=avionicsNoseCone;sasModule?mode=full : Return ALL data for avionicsNoseCone and sasModule parts';
        $info['parts'] = $callback2;
        
        return ['datas'=>$info];
    }
    
    public function getPart()
    {
        $query = $this->parameters['queryFragments'];
        $part_name = $query[0];
        
        $results = $this->getPartsFromRequest([$part_name]);
        
        if(count($results) == 0) {
            return ['datas'=>['no_result' => 'merci de consulter la liste des pieces disponible à l\'url \'/part\'']];
        }

        return ['datas'=> $results];
    }
    
    public function getParts()
    {
        $parts = [];
        if (isset($this->parameters['Query']['part_name'])) {
            $parts_name = $this->parameters['Query']['part_name'];
            $parts = explode(';', $parts_name);
        }
             
        $results = $this->getPartsFromRequest($parts);
        
        if(count($results) == 0) {
            return ['datas'=>['no_result' => 'merci de consulter la liste des pieces disponible à l\'url \'/part\'']];
        }

        return ['datas'=> $results];
    }
    
    private function getPartsFromRequest($parts = []) {
        
        $key_mode = 'light';
        $keys = NULL;
        if (isset($this->parameters['Query']['mode'])) {
            $key_mode = $this->parameters['Query']['mode'];
            $modes = ['full', 'light', 'custom'];
            if(!in_array($key_mode, $modes)) {
                // Error
            }
        }
        
        if (isset($this->parameters['Query']['key_list']) && $key_mode == 'custom') {
            $key_list = $this->parameters['Query']['key_list'];
            $keys = explode(';', $key_list);
        }
        
        if($key_mode == 'light') {
            $keys = ['name','provider','title','description','author','category','subcategory','mass'];
        }
        
        return $this->lib->getProvider('Parts')->getParts($parts, $keys);
        
    }
}
