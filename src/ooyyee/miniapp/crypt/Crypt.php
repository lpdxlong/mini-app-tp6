<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-06-25
 * Time: 10:17
 */

namespace ooyyee\miniapp\crypt;


class Crypt
{
    public $appid;
    public $sessionKey;

    /**
     * 构造函数
     * @param string $appid  小程序的appid
     * @param string $sessionKey  用户在小程序登录后获取的会话密钥
     */
    public function __construct($appid, $sessionKey)
    {
        $this->sessionKey = $sessionKey;
        $this->appid = $appid;
    }

    /**
     * @param $encryptedData
     * @param $iv
     * @return array
     */
    public function decrypt($encryptedData, $iv){
        return ['errcode'=>0,'data'=>[]];
    }
}