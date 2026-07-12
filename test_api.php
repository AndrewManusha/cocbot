<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/clash_api.php';

$result = getClanFromApi('#2CUUJUR0R');

echo '<pre>';
var_dump($result);
echo '</pre>';