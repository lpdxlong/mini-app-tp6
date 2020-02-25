<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-06-24
 * Time: 16:11
 */

namespace ooyyee\miniapp;


use ooyyee\Http;

class Baidu extends MiniApp
{

    /**
     * @param string $code
     * @param array $extra
     * @return array|string
     */
    public function getSessionKey($code,$extra=array())
    {
        $url='https://spapi.baidu.com/oauth/jscode2sessionkey';
        $param = array(
            'client_id'=>$this->appid,
            'sk'=>$this->secret,
            'code'=>$code,
        );
        return  Http::get ( $url.'?'.http_build_query($param) );
    }


    public function accessToken($refresh=false)
    {
        $CACHE_KEY = 'baidu_access_token_' . $this->appid;
        if ($refresh) {
            $url = "https://openapi.baidu.com/oauth/2.0/token?grant_type=client_credentials&client_id={$this->appid}&client_secret={$this->secret}&scope=smartapp_snsapi_base";
            $result = Http::get($url);
            if(isset($result['access_token'])){
                $access_token = $result['access_token'];
                $expires_in = (int) $result['expires_in'];
                cache($CACHE_KEY, $access_token, $expires_in - 200);
                return $access_token;
            }
            return $result;
        }
        return cache($CACHE_KEY) ?:$this->accessToken(true);
    }
}