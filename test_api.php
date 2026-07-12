<?php

require_once __DIR__ . '/clash_api.php';

$result = getClanFromApi('#2CUUJUR0R');

echo '<pre>';
print_r($result);
echo '</pre>';