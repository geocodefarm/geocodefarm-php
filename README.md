# Geocode.Farm PHP SDK

A lightweight, dependency-free PHP SDK for interacting with the [Geocode.Farm](https://geocode.farm) geocoding API.

This SDK allows developers to easily perform **forward** (address to coordinates) and **reverse** (coordinates to address) geocoding using a simple object-oriented interface.

---

## 📦 Installation

You can install the SDK via [Composer](https://getcomposer.org):

```bash
composer require geocodefarm/geocodefarm-php
```

---

## 🚀 Quick Start

```php
<?php

require 'vendor/autoload.php';

use GeocodeFarm\GeocodeClient;

// Initialize the client with your API key
$client = new GeocodeClient('YOUR_API_KEY');

// Forward geocoding: address to lat/lon
$response = $client->forward('30 N Gould St, Sheridan, WY');
print_r($response);

// Reverse geocoding: lat/lon to address
$response = $client->reverse(44.797773, -106.954918);
print_r($response);
```

---

## ✅ Response Format

Both methods (`forward()` and `reverse()`) return an array like this on success:

```php
[
    'success' => true,
    'status_code' => 200,
    'lat' => 44.797773,
    'lon' => -106.954918,
    'accuracy' => 'EXACT_MATCH',
    'full_address' => '30 N Gould St, Sheridan, WY 82801',
    'result' => [
        'house_number' => '30',
        'street_name' => 'N Gould St',
        'locality' => 'Sheridan',
        'admin_2' => 'Sheridan County',
        'admin_1' => 'WY',
        'country' => 'United States',
        'postal_code' => '82801',
        'formatted_address' => '30 N Gould St, Sheridan, WY 82801',
        'latitude' => 44.797773,
        'longitude' => -106.954918,
        'accuracy' => 'EXACT_MATCH'
    ]
]
```

If an error occurs:

```php
[
    'success' => false,
    'status_code' => 403,
    'error' => 'API returned failure: INVALID_KEY'
]
```

---

## 📝 License

This SDK is released under [The Unlicense](https://unlicense.org/) — public domain. Use it freely.

---

## 🤝 Contributing

Pull requests and issues are welcome!

---

## 🌐 About

For more information about the geocoding API, visit [https://geocode.farm](https://geocode.farm).
