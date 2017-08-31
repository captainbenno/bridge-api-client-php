# Sample BRIDGE v3 API PHP client

## Prerequisites

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

## Installation

```bash
php composer.phar install
```

## Variables
Rename the file `config.ini.dist` to `config.ini` and set the config values with the one provided by Leadformance staff.

## Run

```bash
php src/index.php
```

# API Authentication Process
The authentication process needs two headers:
  - **X-Api-Key**: that contains the api key (`clientId` from the config.ini file)
  - **Authorization**: that contains fields for Hawk authentication process (see sections below for further information)

## Api Key
This header allows you to take full advantage of the BRIDGE Cache system. By adding this authentification header, you will be able to access better performance of your request.

## Hawk Protocol
Hawk is an HTTP authentication scheme using a message authentication code (MAC) algorithm to provide partial HTTP request cryptographic verification.

For more details see the [Hawk Repository](https://github.com/hueniverse/hawk).

### How to be Authenticated

You need your `clientId` and your `secret` from the config.ini file. For each request your have to compute a request string based on:
 - header: always `hawk.1.header`
 - timestamp: the number of seconds since January 1, 1970 00:00:00 GMT
 - nonce: a unique computed string in a one minute time window
 - request: the request verb (GET, POST, PUT, DELETE, etc.)
 - url: the url (e.g. /locations) 
 - domaine: `bridge-api.leadformance.com`
 - port: the port use for the request (80)

The signature is compute using HMAC with sha256 hash algorithm and your secret on the request string. Finaly you have to base64 encode the result string to obtain the mac.

Add a Hawk `authorization` header to your request. This header is made up of four parts.
 - id: your `clientId`
 - ts: the `timestamp` use for signature computation
 - nonce: the generated `nonce`
 - mac: the previously computed `mac`

The cURL example below shows a request with Hawk authentication for BRIDGE API:

```bash
curl --request GET \
  --url <CONFIG_FILE.bridgeApiUrl>/locations \
  --header 'accept: application/vnd.bridge+json; version=1' \
  --header 'authorization: Hawk id=\"57f76cf6a4da070f00c58e73_75fa310a-c39d-4006-9145-fe3051c6ff9f\", ts=\"1499337758\", nonce=\"FmtCHR\", mac=\"s8djm5wpt1PocBqnaD1vQ//h84gpHxuvCaNvbdp6WBU=\"' \
  --header 'X-Api-Key: 57f76cf6a4da070f00c58e73_75fa310a-c39d-4006-9145-fe3051c6ff9f' \
  --header 'content-type: application/json'
```

### Hawk Librairies

See below some Hawk libraries you can use to implement client Hawk authentication:

 - [PhP](https://github.com/alexbilbie/PHP-Hawk)
 - [JavaScript](https://github.com/hueniverse/hawk#usage-example)
 - [Python](https://pypi.python.org/pypi/requests-hawk/1.0.0)
 - [Java](https://github.com/wealdtech/hawk)
 - [.Net](https://github.com/pcibraro/hawknet)
