# nia - HTTP request response pattern

Implementation of the request response pattern for the HTTP environment.

## Installation

Require this package with Composer.

```bash
	composer require nia/requestresponse-http
```

## Tests
To run the unit test use the following command:

    $ cd /path/to/nia/component/
    $ phpunit --bootstrap=vendor/autoload.php tests/

## How to use
The following sample shows you how to use the HTTP request response component for a common `index.php` use case. For routing and presenting the `nia/routing` and `nia/presenter` component can be used.

```php
	<?php
	// file: www/index.php
	use Nia\RequestResponse\Http\HttpRequest;
	use Nia\RequestResponse\Http\HttpResponseInterface;
	require_once __DIR__ . '/../vendor/autoload.php';

	$stream = fopen('php://input', 'r');
	$request = new HttpRequest($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES, $stream);

	// [...]
	//
	// routing:
	// [...]
	// controller/presenter:
	// $response = $request->createResponse();
	// $response->set<XYZ>(...);
	//
	// [...]

	$response = $request->createResponse();
	/* @var $response HttpResponseInterface */

	$header = $response->getHeader();

	// add content type if not set.
	if (! $header->has('Content-Type')) {
	    $header->set('Content-Type', 'text/html; charset=utf-8');
	}
	// add content length if not set.
	if (! $header->has('Content-Length')) {
	    $header->set('Content-Length', (string) strlen($response->getContent()));
	}

	// send headers to client.
	foreach ($header as $header => $value) {
	    header($header . ': ' . $value);
	}

	// send cookies to client.
	foreach ($response->getCookies() as $cookie) {
	    $name = $cookie->getName();
	    $value = $cookie->getValue();
	    $expire = $cookie->getExpire()->getTimestamp() - (new DateTime())->getTimestamp();
	    $path = $cookie->getPath();

	    setcookie($name, $value, $expire, $path);
	}

	// send the response.
	http_response_code($response->getStatusCode());
	echo $response->getContent();

	// close stream.
	fclose($stream);

```
