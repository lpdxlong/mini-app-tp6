<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-06-25
 * Time: 11:33
 */

namespace ooyyee\miniapp\crypt;


class ToutiaoCrypt extends Crypt
{
    public function decrypt($encryptedData, $iv){
        $key = base64_decode($this->sessionKey);
        $iv = base64_decode($iv);
        $plaintext = openssl_decrypt(base64_decode($encryptedData), 'AES-128-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        // trim pkcs#7 padding
        $pad = ord(substr($plaintext, -1));
        $pad = $pad < 1 || $pad > 32 ? 0 : $pad;
        $plaintext = substr($plaintext, 0, strlen($plaintext) - $pad);
        return ['errcode'=>0,'data'=>json_decode($plaintext,true)];
    }
}