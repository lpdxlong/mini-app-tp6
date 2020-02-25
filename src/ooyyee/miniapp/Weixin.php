<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-06-24
 * Time: 16:11
 */

namespace ooyyee\miniapp;


use ooyyee\Http;
use ooyyee\Image;

class Weixin extends MiniApp
{

    public $map = [
        'openId' => 'openid',
        'nickName' => 'nickname',
        'avatarUrl' => 'avatar',
        'unionId' => 'unionid',
        'gender' => 'gender',
    ];


    /**
     * @param string $code
     * @param array $extra
     * @return array|string
     */
    public function getSessionKey($code, $extra = array())
    {
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $param = array(
            'appid' => $this->appid,
            'secret' => $this->secret,
            'js_code' => $code,
            'grant_type' => 'authorization_code',
        );
        return Http::get($url . '?' . http_build_query($param));
    }


    public function accessToken($refresh = false)
    {
        $CACHE_KEY = 'weixin_access_token_' . $this->appid;
        if ($refresh) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->secret}";
            $result = Http::get($url);
            if (isset($result['access_token'])) {
                $access_token = $result['access_token'];
                $expires_in = (int)$result['expires_in'];
                cache($CACHE_KEY, $access_token, $expires_in - 200);
                return $access_token;
            }
            return $result;
        }
        return cache($CACHE_KEY) ?: $this->accessToken(true);
    }





    public function sendMessage($data){
        $url='https://api.weixin.qq.com/cgi-bin/message/custom/send';
        return $this->postData($url,json_encode($data,JSON_UNESCAPED_UNICODE));
    }

    public function sendTemplateMessage($data){
        $url='https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send';
        return $this->postData($url,json_encode($data,JSON_UNESCAPED_UNICODE));
    }
    public function getAnalysisDailyVisitTrend($beginTime,$endTime){
        $json=[
            'begin_date'=>date('Ymd',$beginTime),
            'end_date'=>date('Ymd',$endTime)
        ];
        $url='https://api.weixin.qq.com/datacube/getweanalysisappiddailyvisittrend';
        return $this->postData($url,json_encode($json,JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param array $params
     * @param bool $refreshToken
     * @param int $repeatCount
     * @return array|mixed
     * @throws exception\AppException
     * @throws exception\NoAccessTokenException
     */
    public function createQrcode(array $params,$refreshToken=false,$repeatCount=0){
        $url='https://api.weixin.qq.com/wxa/getwxacode';
        $savePath=app()->getRootPath().'public/storage/qrcode/'.$this->getConfig()->getPlatform().'/'.$this->appid.'/';
        if(!is_dir($savePath) &&mkdir($savePath,0755,true)&& !is_dir($savePath)){
            return ['errcode'=>1,'errmsg'=>'dir is not exists'];
        }
        $_params=json_encode($params,JSON_UNESCAPED_SLASHES);
        $fileName=$savePath.md5($_params).'.png';
        if(file_exists($fileName)){
            return ['errcode'=>0,'url'=>Image::fileToURL($fileName),'file'=>$fileName];
        }
        $curl = $this->processAccessToken($url, $refreshToken);
        $result = Http::post($curl, $_params,false);
        $json=@json_decode($result,true);
        if($json){
            if ($repeatCount==0&&isset($json['errcode']) && ($json['errcode'] == '42001' || $json['errcode'] == '40001') ) {            $repeatCount++;
                return $this->createQrcode($params,true,$repeatCount);
            }
            return $json;
        }
        file_put_contents($fileName, $result);
        return ['errcode'=>0,'url'=>Image::fileToURL($fileName),'file'=>$fileName];
    }
}