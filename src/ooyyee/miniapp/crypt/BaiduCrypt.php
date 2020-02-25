<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-06-25
 * Time: 10:15
 */

namespace ooyyee\miniapp\crypt;


class BaiduCrypt extends Crypt
{


    /**
     *
     * @param string $encryptedData    待解密数据，返回的内容中的data字段
     * @param string $iv            加密向量，返回的内容中的iv字段
     * @return array
     */
   public  function decrypt($encryptedData, $iv) {
        $session_key = base64_decode($this->sessionKey);
        $iv = base64_decode($iv);
       $aesCipher = base64_decode($encryptedData);


       $plaintext = openssl_decrypt($aesCipher, 'AES-192-CBC', $session_key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        if ($plaintext == false) {
            return ['errcode'=>2];
        }

        // trim pkcs#7 padding
        $pad = ord(substr($plaintext, -1));
        $pad = ($pad < 1 || $pad > 32) ? 0 : $pad;
        $plaintext = substr($plaintext, 0, strlen($plaintext) - $pad);

        // trim header
        $plaintext = substr($plaintext, 16);
        // get content length
        $unpack = unpack("Nlen/", substr($plaintext, 0, 4));
        // get content
        $content = substr($plaintext, 4, $unpack['len']);
        // get app_key
        $app_key_decode = substr($plaintext, $unpack['len'] + 4);

        return $this->appid == $app_key_decode ? ['errcode'=>0,'data'=>json_decode($content,true)]: ['errcode'=>1];
    }
}