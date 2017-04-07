<?php
namespace Bridge;

require __DIR__ . '/../vendor/autoload.php';

use Bridge\Auth\AuthProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

// Config
$clientId = '<INSERT CLIENT ID HERE>';
$secret = '<INSERT CLIENT SECRET HERE>';
$tokenEndpoint = '<INSERT SSO TOKEN ENDPOINT HERE>';
$bridgeApiUrl = '<INSERT BRIDGE API URL HERE>';

// Retrieve access token
$provider = new AuthProvider($clientId, $secret, $tokenEndpoint);
$authInfo = $provider->getToken();
expiresIn($authInfo);

// Create Client
$api = new Client([
    'base_uri' => $bridgeApiUrl,
    'headers' => [
        'Authorization' => 'Bearer ' . $authInfo['access_token'],
        'Accept' => 'application/vnd.bridge-v1+json'
    ],
]);

// List locations
$list = decodeResponse($api->get("/locations"));
println("Total: {$list['total']} locations");

// Create a new Location
$data = [
    'name' => 'Leadformance',
    'localisation' => [
        'address1' => '19 Rue du Lac Saint-André',
        'city' => 'Le Bourget-du-Lac',
        'postalCode' => '73370',
        'countryCode' => 'FR'
    ]
];
$createdLocation = sendRequest($api, 'post', '/locations', [
    'json' => $data
]);
println("New location created | ID: {$createdLocation['_id']}");

// Get the created location
$location = sendRequest($api, 'get', "/locations/{$createdLocation['_id']}");
println("Locationretrieved | ID: {$location['_id']}");

// // Update a location
println("Adding website to location {$location['_id']}");
$location['website'] = 'https://www.leadformance.com';

// Update a Location
$updatedLocation = sendRequest($api, 'put', "/locations/{$location['_id']}", [
    'json' => $location
]);
println("Location updated, new website is {$updatedLocation['website']}");


function sendRequest($api, $method, $url, $parameters = []) {
    try {
        $response = $api->$method($url, $parameters);
        return decodeResponse($response);
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        println("Error sending request $method $url");
        if ($e->hasResponse()) {
            echo Psr7\str($e->getResponse());
        }
        exit(1);
    }
}

function decodeResponse($response) {
    $status = $response->getStatusCode();
    $body = (string)$response->getBody();
    if (in_array($status, [200,201])) {
        return json_decode($body, true);
    } else {
        println("Error response received from API with status $status");
        var_dump($body);
        exit(1);
    }
}

function println($line) {
    echo "$line\n";
}

function expiresIn($info) {
    $d1 = new \DateTime();
    $d2 = new \DateTime();
    $d2->add(new \DateInterval('PT'.$info['expires_in'].'S'));

    println($d2->diff($d1)->format("Token expires in %a days, %h hours, %i minutes, %s seconds"));
}
