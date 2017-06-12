<?php

namespace Bridge\Auth;

use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Middleware;
use Dflydev\Hawk\Client\ClientBuilder as HawkClient;
use Dflydev\Hawk\Credentials\Credentials as HawkCredential;

class HawkMiddleware
{
    /**
     * @var HawkClient
     */
    protected $hawkBuilder;

    /**
     * @var HawkCredential
     */
    protected $credentials;

    function __construct($key, $secret, $algorithm = 'sha256')
    {
        $this->hawkBuilder = HawkClient::create()->build();
        $this->credentials = new HawkCredential($secret, $algorithm, $key);
    }

    function getHandler()
    {
        return Middleware::mapRequest(function (RequestInterface $request) {
            $hawkRequest= $this->hawkBuilder->createRequest($this->credentials, (string) $request->getUri(), $request->getMethod());
            return $request->withHeader($hawkRequest->header()->fieldName(), $hawkRequest->header()->fieldValue());
        });
    }
}
