<?php
/**
 * simple-php-encrypter-decrypter
 *
 * @author      Reinhard Hiebl <reinhard@hieblmedia.com>
 * @copyright   Copyright (C) 2020, HieblMedia (Reinhard Hiebl)
 * @license     MIT
 * @link        https://github.com/hieblmedia/simple-php-encrypter-decrypter
 */

namespace HieblMedia\Encryption;

use InvalidArgumentException;

/**
 * Class Encrypter
 * @package Encryption
 */
class Encrypter
{
    private $secureKey;
    protected $method = 'AES-256-CBC';

    /**
     * @param string|null $secureKey Optional, if empty a random unique key will be generated
     */
    public function __construct($secureKey = null)
    {
        $this->createSecureKey($secureKey);
    }

    /**
     * Possible to change key on fly to jump over multiple encoding/decoding
     *
     * @param $secureKey
     * @throws InvalidArgumentException
     */
    public function setSecureKey($secureKey)
    {
        if (!empty($secureKey)) {
            $this->createSecureKey($secureKey);
        } else {
            throw new InvalidArgumentException('$secureKey can not be empty');
        }
    }

    /**
     * Possible to change cipher method on fly to jump over multiple encoding/decoding
     *
     * @param $method
     * @throws InvalidArgumentException
     */
    public function setMethod($method)
    {
        if (!empty($method)) {
            $ciphers = openssl_get_cipher_methods(true);
            if (in_array($method, $ciphers)) {
                $this->method = $method;
            } else {
                throw new InvalidArgumentException('Cipher $method was not found in openssl_get_cipher_methods(). Please select another method ask your server administrator to add your required method');
            }
        } else {
            throw new InvalidArgumentException('$method can not be empty');
        }
    }

    /**
     * createSecureKey
     *
     * @param $secureKey
     */
    private function createSecureKey($secureKey)
    {
        if (!$secureKey) {
            $secureKey = (microtime(true) . mt_rand(10000, 90000));
        }
        $secureKey = md5($secureKey);

        $this->secureKey = pack('H*', $secureKey);
    }

    /**
     * encode
     *
     * @param $value
     * @return bool|string
     */
    function encode($value)
    {
        $ivSize = openssl_cipher_iv_length($this->method);
        $iv = openssl_random_pseudo_bytes($ivSize);

        $encrypted = openssl_encrypt($value, $this->method, $this->secureKey, OPENSSL_RAW_DATA, $iv);

        // For storage/transmission, we simply concatenate the IV and cipher text
        $encrypted = $this->safeBase64Encode($iv . $encrypted);

        return $encrypted;
    }

    /**
     * decode
     *
     * @param $value
     * @return bool|string
     */
    function decode($value)
    {
        $value = $this->safeBase64Decode($value);
        $ivSize = openssl_cipher_iv_length($this->method);
        $iv = substr($value, 0, $ivSize);
        $value = openssl_decrypt(substr($value, $ivSize), $this->method, $this->secureKey, OPENSSL_RAW_DATA, $iv);

        return $value;
    }

    /**
     * safeBase64Encode
     *
     * @param $string
     * @return mixed|string
     */
    public function safeBase64Encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    /**
     * safeBase64Decode
     *
     * @param $string
     * @return string
     */
    public function safeBase64Decode($string)
    {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
}
