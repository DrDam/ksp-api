<?php
header("Access-Control-Allow-Origin: *");
require_once __DIR__ . '/vendor/autoload.php'; // Autoload files using Composer autoload

use Symfony\Component\HttpFoundation\Request;
use Japloora\Japloora;

$request = Request::createFromGlobals();

$debug = false;
$JaplooraCore = new Japloora($request, $debug);

$result = $JaplooraCore->run();

print $result;
