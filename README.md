# Simple PHP Encrypter/Decrypter [![Build Status](https://travis-ci.org/hieblmedia/simple-php-encrypter-decrypter.png?branch=master)](https://travis-ci.org/hieblmedia/simple-php-encrypter-decrypter) #

Encrypter is a simple class to encode/decode data with an secure key.

## Installing via Composer ##

The recommended way to install it through [Composer](http://getcomposer.org).

### Install Composer ###
    $ curl -sS https://getcomposer.org/installer | php

Or if you don't have curl:

    $ php -r "eval('?>'.file_get_contents('https://getcomposer.org/installer'));"

### Add as dependency ###

    $ php composer.phar require hieblmedia/simple-php-encrypter-decrypter:dev-master

After installing, you need to require Composer's autoloader (if not already present):

```php
<?php

require 'vendor/autoload.php';

// ...
```

## Usage ##

``` php
<?php

$value = 'My String';

// Get encrypter with random secure key
$encrypter = new \Encryption\Encrypter;

$encodedValue = $encrypter->encode($value);
echo "Encoded value: $encodedValue\n"; // Encrypted value

$decodedValue = $encrypter->decode($encodedValue);
echo "Decoded value: $decodedValue\n"; // My String
```
### Use your own fixed secure key ###

``` php
<?php

$encrypter = new \Encryption\Encrypter('yourFixedSecureKey');

// ...
```

## Tests ##

You can run the unit tests with the following command:

    $ cd path/to/Encrypter/
    $ php composer.phar install --dev
    $ phpunit
