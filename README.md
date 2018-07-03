# Amazon Alexa PHP Library

[![Build Status](https://travis-ci.org/vasily-kartashov/amazon-alexa-php.svg?branch=master)](https://travis-ci.org/vasily-kartashov/amazon-alexa-php)

This library provides provides some interface for developing Amazon Alexa Skills for your PHP app.

Consider this library work in progress and subject to rather significant API changes in the nearest future. Current implementation is garbage, but there's almost no alternative out there.

## Usage 

Install via composer: `vasily-kartashov/amazon-alexa-php`.

### Roadmap

- Remove public access
- Add more unit tests
- Deprecate the old confusing API
- Add dependency on standard HTTP request object
- Add integration with Oauth-Server from php-league 
- ~~Stop throwing exceptions around~~
- Add LoggerAwareInterface
- Extract Certificate validation authority and add cacheing to it
- Add validator and dependency on https://github.com/alexa/alexa-smarthome/tree/master/validation_schemas




### Requests

Create request by using a factory method `Request::fromHttpRequest` that accepts objects of type `RequestInterface` as defined by PSR-7. 
For example when using Guzzle one can initialize an Alexa request object by running:

```php
$request = Request::fromHttpRequest(ServerRequest::fromGlobals(), $applicationId);
```






### Certificate validation
By default the system validates the request signature by fetching Amazon's signing certificate and decrypting the signature. You need CURL to be able to get the certificate. No caching is done but you can override the Certificate class easily if you want to implement certificate caching yourself based on what your app provides:

Here is a basic example:
```php
class MyAppCertificate extends \Alexa\Request\Certificate {
  public function getCertificate() {
    $cached_certificate = retrieve_cert_from_myapp_cache();
    if (empty($cached_certificate)) {
      // Certificate is not cached, download it
      $cached_ertificate = $this->fetchCertificate();
      // Cache it now
    }
    return $cached_certificate;
  }
}
```

And then in your app, use the setCertificateDependency function:

```php
$certificate = new MyAppCertificate($_SERVER['HTTP_SIGNATURECERTCHAINURL'], $_SERVER['HTTP_SIGNATURE']);

$alexa = new \Alexa\Request\Request($rawRequest);
$alexa->setCertificateDependency($certificate);

$alexaRequest = $alexa->fromData();
```

### Application Id validation
The library will automatically validate your Application Id matches the one of the incoming request - you don't need to do anything for that. If and only if you wish to change how the validation happens, you might use a similar scenario to the certificate validation - provide your own Application class extending the \Alexa\Request\Application and providing a validateApplicationId() function as part of that. Pass your application to the Request library in a same way as the certificate:
```php

$application = new MyAppApplication($myappId);
$alexa = new \Alexa\Request\Request($rawRequest, $myappId);
$alexa->setApplicationDependency($application);

$alexaRequest = $alexa->fromData();
```


### Response
You can build an Alexa response with the `Response` class. You can optionally set a card or a reprompt too.

Here's a few examples.
```php
$response = new \Alexa\Response\Response;
$response->respond('Cooool. I\'ll lower the temperature a bit for you!')
	->withCard('Temperature decreased by 2 degrees');
```

```php
$response = new \Alexa\Response\Response;
$response->respond('What is your favorite color?')
	->reprompt('Please tell me your favorite color');
```

To output the response, simply use the `->render()` function, e.g. in Laravel you would create the response like so:
```php
return response()->json($response->render());
```

In vanilla PHP:
```php
header('Content-Type: application/json');
echo json_encode($response->render());
exit;
```

