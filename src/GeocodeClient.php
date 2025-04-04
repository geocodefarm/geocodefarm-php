<?php
namespace GeocodeFarm;

class GeocodeClient
{
    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function forward(string $address): array
    {
        $url = 'https://api.geocode.farm/forward/';
        $params = ['key' => $this->apiKey, 'addr' => $address];

        return $this->handleResponse($this->makeRequest($url, $params), 'forward');
    }

    public function reverse(float $lat, float $lon): array
    {
        $url = 'https://api.geocode.farm/reverse/';
        $params = ['key' => $this->apiKey, 'lat' => $lat, 'lon' => $lon];

        return $this->handleResponse($this->makeRequest($url, $params), 'reverse');
    }

    private function makeRequest(string $url, array $params): array
    {
        $query = http_build_query($params);
        $fullUrl = $url . '?' . $query;

        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'timeout' => 10,
                'header'  => "User-Agent: GeocodeFarmSDK/1.0\r\n"
            ]
        ]);

        $response = @file_get_contents($fullUrl, false, $context);

        if ($response === false) {
            return ['http_status' => 0, 'error' => 'Request failed or timed out'];
        }

        $statusCode = $http_response_header[0] ?? '';
        preg_match('#HTTP/\d+\.\d+ (\d+)#', $statusCode, $matches);
        $httpCode = (int)($matches[1] ?? 0);

        $data = json_decode($response, true);
        return ['http_status' => $httpCode, 'data' => $data];
    }

    private function handleResponse(array $response, string $type): array
    {
        if (!isset($response['data']) || !is_array($response['data'])) {
            return [
                'success' => false,
                'status_code' => $response['http_status'],
                'error' => $response['error'] ?? 'Invalid response from server'
            ];
        }

        $data = $response['data'];

        $status = $data['STATUS']['status'] ?? 'FAILED';
        if ($status !== 'SUCCESS') {
            return [
                'success' => false,
                'status_code' => $response['http_status'],
                'error' => 'API returned failure: ' . ($data['STATUS']['status'] ?? 'Unknown')
            ];
        }

        $result = [];
        if ($type === 'reverse') {
            $resultData = $data['RESULTS']['result'][0] ?? [];
            $result = [
                'house_number' => $resultData['house_number'] ?? null,
                'street_name' => $resultData['street_name'] ?? null,
                'locality' => $resultData['locality'] ?? null,
                'admin_2' => $resultData['admin_2'] ?? null,
                'admin_1' => $resultData['admin_1'] ?? null,
                'country' => $resultData['country'] ?? null,
                'postal_code' => $resultData['postal_code'] ?? null,
                'formatted_address' => $resultData['formatted_address'] ?? null,
                'latitude' => $resultData['latitude'] ?? null,
                'longitude' => $resultData['longitude'] ?? null,
            ];
            $result['accuracy'] = $data['RESULTS']['result']['accuracy'] ?? null;
        } else {
            $resultData = $data['RESULTS']['result'] ?? [];
            $coordinates = $resultData['coordinates'] ?? [];
            $result = [
                'house_number' => $resultData['address']['house_number'] ?? null,
                'street_name' => $resultData['address']['street_name'] ?? null,
                'locality' => $resultData['address']['locality'] ?? null,
                'admin_2' => $resultData['address']['admin_2'] ?? null,
                'admin_1' => $resultData['address']['admin_1'] ?? null,
                'country' => $resultData['address']['country'] ?? null,
                'postal_code' => $resultData['address']['postal_code'] ?? null,
                'formatted_address' => $resultData['address']['full_address'] ?? null,
                'latitude' => $coordinates['lat'] ?? null,
                'longitude' => $coordinates['lon'] ?? null
            ];
            $result['accuracy'] = $resultData['accuracy'] ?? null;
        }

        return [
            'success' => true,
            'status_code' => $response['http_status'],
            'lat' => $result['latitude'] ?? null,
            'lon' => $result['longitude'] ?? null,
            'accuracy' => $result['accuracy'] ?? null,
            'full_address' => $result['formatted_address'] ?? null,
            'result' => $result
        ];
    }
}
