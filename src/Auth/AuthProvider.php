<?php
namespace Bridge\Auth;

use GuzzleHttp\Client;

class AuthProvider {
    protected $clientId;
    protected $secret;
    protected $basicAuth;
    protected $tokenEndpoint;
    protected $client;

    public function __construct($clientId, $secret, $tokenEndpoint) {
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->basicAuth = 'Basic ' . base64_encode("$this->clientId:$this->secret");
        $this->tokenEndpoint = $tokenEndpoint;
        $this->client = new Client([
            'base_uri' => $this->tokenEndpoint
        ]);
    }

    public function getToken() {
        $response = $this->client->request('POST', $this->tokenEndpoint, [
            'headers' => [
                'Authorization' => $this->basicAuth
            ],
            'form_params' => [
                'grant_type' => 'client_credentials'
            ]
        ]);

        if (200 == $response->getStatusCode()) {
            return json_decode((string) $response->getBody(), true);
        }

        throw new Exception($response->getReasonPhrase());
    }
}
