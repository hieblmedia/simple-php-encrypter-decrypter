<?php
/**
 * simple-php-encrypter-decrypter
 *
 * @author      Reinhard Hiebl <reinhard@hieblmedia.com>
 * @copyright   Copyright (C) 2013, HieblMedia (Reinhard Hiebl)
 * @license     MIT
 * @link        https://github.com/hieblmedia/simple-php-encrypter-decrypter
 */

namespace Encryption;

/**
 * Class Encrypter
 * @package Encryption
 */
class Encrypter
{
    private $secureKey;

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
     */
    public function setSecureKey($secureKey)
    {
        $this->secureKey = $secureKey;
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
        $secureKey = sha1($secureKey);

        $this->secureKey = pack('H*', $secureKey);
    }

    /**
     * encode
     *
     * @param $value
     * @return bool|string
     */
    public function encode($value)
    {
        if ($value === '' || !is_scalar($value)) {
            return false;
        }

        $text = $value;
        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        $cryptText = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->secureKey, $text, MCRYPT_MODE_ECB, $iv);

        return trim($this->safeBase64Encode($cryptText));
    }

    /**
     * decode
     *
     * @param $value
     * @return bool|string
     */
    public function decode($value)
    {
        if ($value === '' || !is_scalar($value)) {
            return false;
        }

        $cryptText = $this->safeBase64Decode($value);
        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        $decryptText = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->secureKey, $cryptText, MCRYPT_MODE_ECB, $iv);

        return trim($decryptText);
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
