<?php
/**
 * simple-php-encrypter-decrypter
 *
 * @author      Reinhard Hiebl <reinhard@hieblmedia.com>
 * @copyright   Copyright (C) 2013, HieblMedia (Reinhard Hiebl)
 * @license     MIT
 * @link        https://github.com/hieblmedia/simple-php-encrypter-decrypter
 */

namespace Encryption\Test;

use Encryption\Encrypter;

class FixedSecureKeyTest extends \PHPUnit_Framework_TestCase
{
    private $secureKey = 'my_fixed_secure_key_test';

    /**
     * Test original value is equal self decoded value.
     */
    public function testEqualValueDecoded()
    {
        $encrypter = new Encrypter($this->secureKey);

        $originalValue = $this->generateRandomString();
        $encodedValue = $encrypter->encode($originalValue);
        $decodedValue = $encrypter->decode($encodedValue);

        $this->assertEquals($decodedValue, $originalValue);
    }

    /**
     * Test with second Encrypter and check the encoded value is equal, because secure key is fixed.
     */
    public function testEqualSecondEncrypterFixed()
    {
        $encrypter = new Encrypter($this->secureKey);
        $encrypter2 = new Encrypter($this->secureKey);

        $originalValue = $this->generateRandomString();
        $encodedValue1 = $encrypter->encode($originalValue);
        $encodedValue2 = $encrypter2->encode($originalValue);

        $this->assertEquals($encodedValue1, $encodedValue2);
    }

    /**
     * generateRandomString
     *
     * @param int $length
     * @return string
     */
    protected function generateRandomString($length = 255)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}
