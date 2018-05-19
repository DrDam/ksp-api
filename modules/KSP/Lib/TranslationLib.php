<?php

namespace KSP;

use KSP\ParsorTranslations;
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
    
     public function __construct($do_reset = false, $dataProvides = []) {
        $this->dataFile = self::getProviderName();
        parent::__construct($do_reset);
    }
    
    protected function makeData($dataProvides = [])
    {
        $translation_pasror = new ParsorTranslations();
        $this->data = $translation_pasror->parse();
    }
    
    public function getLocals() {
        return ['locals' => $this->data['locals']];
    }
    
    public function getTranslations($lang, $keys) {
        $translations = [];
        
        foreach($keys as $key) {
            if(isset($this->data['strings']['#'.$key])) {
                $item = $this->data['strings']['#'.$key];
                $translations[$key] = $item[$lang];
            }
        }
        
        $output = [];
        $output['langcode'] = $lang;
        $output['translations'] = $translations;
        return $output;
    }
}
