<?php

namespace KSP;

/**
 *
 * @author drdam
 */
interface KSPDataInterface {

    public static function getProviderName();
    public function dump();
    public function __construct($do_reset = false);
    
}
