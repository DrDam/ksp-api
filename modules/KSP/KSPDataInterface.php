<?php

namespace KSP;

/**
 *
 * @author drdam
 */
interface KSPDataInterface {

    public static function getProviderName();
    public function dump($sub = '');
    public function __construct($do_reset = false);
    
}
