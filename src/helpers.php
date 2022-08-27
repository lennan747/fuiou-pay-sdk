<?php

namespace Lennan\Fuiou\Sdk;


/**
 * Generate a signature.
 *
 * @param array $attributes
 * @param string $key
 * @param string $encryptMethod
 *
 * @return string
 */
function generate_sign(array $attributes, $key, string $encryptMethod = 'md5'): string
{
    ksort($attributes);

    if ($encryptMethod == 'openssl') {
        openssl_sign(http_build_query($attributes), $sign, $key, OPENSSL_ALGO_MD5);
        return base64_encode($sign);
    }

    $attributes['key'] = $key;
    return strtoupper(call_user_func_array($encryptMethod, [urldecode(http_build_query($attributes))]));
}

/**
 * @param $publicKey
 * @return mixed
 */
function get_public_key($publicKey)
{
    $publicKey = "-----BEGIN PUBLIC KEY-----\n" .
        wordwrap($publicKey, 64, "\n", true) .
        "\n-----END PUBLIC KEY-----";
    return openssl_pkey_get_public($publicKey);
}

/**
 * @param $privateKey
 * @return mixed
 */
function get_private_key($privateKey)
{
    $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n" .
        wordwrap($privateKey, 64, "\n", true) .
        "\n-----END RSA PRIVATE KEY-----";
    $result = openssl_pkey_get_private($privateKey);
    return $result;

}

/**
 * Get client ip.
 *
 * @return string
 */
function get_client_ip()
{
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        // for php-cli(phpunit etc.)
        $ip = defined('PHPUNIT_RUNNING') ? '127.0.0.1' : gethostbyname(gethostname());
    }

    return filter_var($ip, FILTER_VALIDATE_IP) ?: '127.0.0.1';
}