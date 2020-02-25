<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-06-25
 * Time: 10:56
 */

namespace ooyyee\miniapp;


use ooyyee\Http;

class Toutiao extends MiniApp
{
    /**
     * @param string $code
     * @param array $extra
     * @return array|string
     */
    public function getSessionKey($code,$extra=array())
    {
        $url = 'https://developer.toutiao.com/api/apps/jscode2session';
        $data = [
            'appid' => $this->appid,
            'secret' => $this->secret,
            'code' => $code,
        ];
        if (isset($extra['anonymous_code'])) {
            $data['anonymous_code'] = $extra['anonymous_code'];
        }
       return Http::get($url.'?'.http_build_query($data));
    }



    public function accessToken($refresh = false)
    {
        $CACHE_KEY = 'toutiao_access_token_' . $this->appid;
        if ($refresh) {
            $url = "https://developer.toutiao.com/api/apps/token?appid={$$this->appid}&secret={$$this->secret}&grant_type=client_credential";
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