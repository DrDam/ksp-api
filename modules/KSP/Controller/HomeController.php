<?php

namespace KSP;

use Japloora\ControllerBase;
use Japloora\Config;
use KSP\KSPDataLib;
use Japloora\Base;

class HomeController extends ControllerBase
{

    public static function defineRoutes()
    {
        return array(
            array(
                'path' => '/',
                'scheme' => [ROUTE_PARAMETER_SCHEME_HTTP, ROUTE_PARAMETER_SCHEME_HTTPS],
                'method' => [ROUTE_PARAMETER_METHOD_GET],
                'callback' => 'Home',
            ),
            array(
                'path' => 'dump',
                'scheme' => [ROUTE_PARAMETER_SCHEME_HTTP, ROUTE_PARAMETER_SCHEME_HTTPS],
                'method' => [ROUTE_PARAMETER_METHOD_GET],
                'callback' => 'getDump',
                'parameters' => [
                     'reset' => [
                        'mandatory' => ROUTE_PARAMETER_OPTIONAL,
                        'type' => ROUTE_PARAMETER_TYPE_STRING
                     ],
                    ]
            ),
        );
    }
    
    public function Home()
    {
        $infos = [];
        $defined_controllers = Base::getExtends('Controller');
        foreach ($defined_controllers as $classname) {
            if(strstr($classname, 'KSP')) {
                if(method_exists($classname, 'getHome')) {
                    $controller = new $classname();
                    $info = $controller->getHome();
                    foreach($info['datas'] as $path => $item) {
                        $infos[$path] = $item;
                    }

                }
            }
        }
        
        $out = [];
        $out['title'] = 'Welcome in KSP-API';
        $out['version'] = '1.0';
        $out['description'] = 'You\'ll find below the list of defined callbacks';
        $out['callbacks'] = $infos;
        return ['datas'=>$out];
    }
    
    public function getDump()
    {
        $do_reset = false;
        if (isset($this->parameters['Query']['reset'])) {
            $reset = $this->parameters['Query']['reset'];
            if ($reset == 'reset') {
                $do_reset = true;
            }
        }
        $datas = new KSPDataLib($do_reset);
        return ['datas'=>$datas->dump()];
    }
}
