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
        $callback1['title'] = 'Detail d\'une pièce';
        $callback1['description'] = 'Retourne toute les informations connus d\'une pièce ( l\'identifiant de la pièce est sensible à la casse)';
        $callback1['exemples'][] = '/part/sasModule : retourne les information de la piece sasModule.';
        $callback1['option'][] = 'Le paramètre \'mode\' est optionel ( light par défaut, full, custom).';
        $callback1['option'][] = 'Le paramètre \'key_list\' est obligatoire pour le mode \'custom\' contient la liste des attributs séparées par un point-virgule \';\'.';        
        $info['part/*'] = $callback1;
        
        $callback2 = [];
        $callback2['title'] = 'Détail de plusieurs pièces';
        $callback2['description'] = 'Retourne toute les informations connus de plusieurs pièces en un appel';
        $callback2['option'][] = 'Le paramètre \'part_name\' est obligatoire contient la liste des pièces demandée séparées par un point-virgule \';\'.';
        $callback2['option'][] = 'Le paramètre \'mode\' est optionel ( light par défaut, full, custom).';
        $callback2['option'][] = 'Le paramètre \'key_list\' est obligatoire pour le mode \'custom\' contient la liste des attributs séparées par un point-virgule \';\'.';        
        $callback2['exemples'][] = '/parts?part_name=avionicsNoseCone;sasModule : retourne le détail des pièces avionicsNoseCone et sasModule';
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
        
        return $this->lib->getParts($parts, $keys);
        
    }
}
