<?php

namespace KSP\Part;

class ModulesPart
{

    private $translations =
    [
        'ModuleEngines'=> 'engines',
        'ModuleEnginesFX' => 'engines',
        'ModuleRCS'=> 'rcs',
        'ModuleRCSFX' => 'rcs'
    ];

    public function getModule($modules_name)
    {
        return (isset($this->translations[$modules_name])) ? $this->translations[$modules_name] : strtolower(str_replace('Module', '', $modules_name));
    }

}
