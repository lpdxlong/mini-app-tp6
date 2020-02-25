<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-06-25
 * Time: 10:11
 */

namespace ooyyee\miniapp\crypt;


class WeixinCrypt extends Crypt
{


    public static $OK = 0;
    public static $IllegalAesKey = -41001;
    public static $IllegalIv = -41002;
    public static $IllegalBuffer = -41003;
    public static $DecodeBase64Error = -41004;






    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @return array 结果
     */
    public function decrypt($encryptedData, $iv)
    {
        if (strlen($this->sessionKey) != 24) {
            return ['errcode'=>self::$IllegalAesKey];
        }
        $aesKey = base64_decode($this->sessionKey);


        if (strlen($iv) != 24) {
            return ['errcode'=>self::$IllegalIv];
        }
        $aesIV = base64_decode($iv);

        $aesCipher = base64_decode($encryptedData);

        $result = openssl_decrypt($aesCipher, 'AES-128-CBC', $aesKey, 1, $aesIV);

        $dataObj = json_decode($result);
        if ($dataObj == NULL) {
            return ['errcode'=>self::$IllegalBuffer];
        }
        if ($dataObj->watermark->appid != $this->appid) {
            return ['errcode'=>self::$IllegalBuffer];
        }
        return ['errcode'=>self::$OK,'data'=>json_decode($result,true)];
    }


}