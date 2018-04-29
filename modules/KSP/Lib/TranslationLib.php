<?php

namespace KSP;

use KSP\ParsorPart;
use KSP\KSPDataLibBase;
use KSP\KSPDataInterface;
/**
 * Description of KSPData
 *
 * @author drdam
 */
class TranslationLib  extends KSPDataLibBase implements KSPDataInterface 
{
    public static function getProviderName() {
        return 'Translations';
    }
    
     public function __construct($do_reset = false) {
        $this->dataFile = self::getProviderName();
        parent::__construct($do_reset);
    }
    
    protected function makeData()
    {
        $this->data = [];
    }
    
}
