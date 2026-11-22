<?php
ini_set('display_errors', '0');
require __DIR__ . '/../../../vendor/autoload.php';

$openapi = \OpenApi\Generator::scan([
  __DIR__ . '/doc_setup.php',
  __DIR__ . '/../../../routes',
]);

header('Content-Type: application/json');
echo $openapi->toJson();
