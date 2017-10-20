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
Authentication is achieved via an API key.
Your API key is available in the config.ini file.

The mandatory `X-Api-Key` header allows you to take full advantage of the BRIDGE Cache system.

### How to be Authenticated

Pass the API Key in all requests in the `x-api-key` header, like this:

```bash
curl --request GET \
  --url <CONFIG_FILE.bridgeApiUrl>/locations \
  --header 'accept: application/vnd.bridge+json; version=1' \
  --header 'X-Api-Key: 57f76cf6a4da070f00c58e73_75fa310a-c39d-4006-9145-fe3051c6ff9f' \
  --header 'content-type: application/json'
```
