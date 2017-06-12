<?php
namespace Bridge;

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use Bridge\Auth\HawkMiddleware;
use GuzzleHttp\Middleware;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

// Config
$config = parse_ini_file(__DIR__ . "/../config.ini");

// Create Client
$api = buildClient($config);

// List locations
$list = sendRequest($api, 'get', '/locations');
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


function buildClient($config) {
    $stack = new HandlerStack();
    $stack->setHandler(new CurlHandler());

    // Add Middlewares
    $middleware = new HawkMiddleware($config['clientId'], $config['secret']);
    $stack->push($middleware->getHandler());
    $stack->push(decodeResponse());

    // Create Client
    return new Client([
        'base_uri' => $config['bridgeApiUrl'],
        'headers' => [
            'Accept' => 'application/vnd.bridge+json; version=1'
        ],
        'handler' => $stack
    ]);
}

function sendRequest($api, $method, $url, $parameters = []) {
    try {
        return $api->$method($url, $parameters);
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        println("Error sending request $method $url");
        if ($e->hasResponse()) {
            echo Psr7\str($e->getResponse());
        } else {
            echo $e->getMessage();
        }
    } catch (\Exception $e) {
        echo $e->getMessage();
        exit(1);
    }
}

function decodeResponse() {
    return Middleware::mapResponse(function (ResponseInterface $response) {
        $status = $response->getStatusCode();
        $body = (string)$response->getBody();

        if (in_array($status, [200,201])) {
            return json_decode($body, true);
        }

        throw new \Exception($body);
    });
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
