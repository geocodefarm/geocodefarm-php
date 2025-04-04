<?php
require 'vendor/autoload.php';

use GeocodeFarm\GeocodeClient;

$client = new GeocodeClient('YOUR-API-KEY-HERE');

// Forward
$result = $client->forward('30 N Gould St, Sheridan, WY');
print_r($result);

// Reverse
$result = $client->reverse(44.7977733966548, -106.954917523499);
print_r($result);
