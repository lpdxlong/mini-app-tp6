<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-06-24
 * Time: 16:09
 */

namespace ooyyee\miniapp;


use ooyyee\Http;
use ooyyee\miniapp\crypt\Crypt;
use ooyyee\miniapp\exception\AppException;
use ooyyee\miniapp\exception\NoAccessTokenException;
use ooyyee\miniapp\exception\NotSupportException;


abstract class MiniApp
{
    /**
     * @var Config
     */
    private $config;
    public $appid;
    public $secret;

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function __construct(Config $config)
    {
        $this->config=$config;
        $this->appid=$config->getAppid();
        $this->secret=$config->getSecret();
    }




    /**
     * @param Config $config

     * @return MiniApp
     * @throws NotSupportException
     */
    public static function createApp(Config $config){
        if(!in_array($config->getPlatform(),['weixin','baidu','alipay','toutiao'])){
            throw new NotSupportException($config->getPlatform());
        }

        $className= parse_name($config->getPlatform(),1);
        $className=__NAMESPACE__.'\\'.$className;
        return new $className($config);
    }

    /**
     * 获取SessionKey
     * @param string $code
     * @param array $extra
     * @return array
     */
    abstract public function getSessionKey($code,$extra=array());

    /**
     * 解密
     * @param $data
     * @param $iv
     * @param $sessionKey
     * @return array
     */
    public function decrypt($data,$iv,$sessionKey){
        $className=ucfirst($this->getConfig()->getPlatform().'Crypt');
        $className=__NAMESPACE__.'\\crypt\\'.$className;
        $crypt=new $className($this->appid,$sessionKey);
        if($crypt instanceof Crypt){
            return $crypt->decrypt($data,$iv);
        }
        return array('errcode'=>404);
    }
    abstract public function accessToken($refresh=false);


    /**
     * 自动添加access_token 参数<br/>
     * 如果获取access_token 成功 返回带access_token 参数的URL <br/>如果不成功 抛出异常
     *
     * @param string $url
     * @param boolean $refreshToken
     * @param array $extra
     * @throws NoAccessTokenException|AppException
     * @return string 返回带access_token 参数的URL
     */
    public function processAccessToken($url, $refreshToken, $extra = array())
    {
        $set = parse_url($url);
        $params = array();
        if (! empty($set['query'])) {
            parse_str($set['query'], $params);
        }
        if (empty($set['path'])) {
            $set['path'] = '/';
        }
        $access_token = $this->accessToken($refreshToken);
        if ($access_token && is_string($access_token)) {
            $params['access_token'] = $access_token;
            $params = array_merge($params, $extra);
            $args = http_build_query($params);
            return $set['scheme'] . '://' . $set['host'] . $set['path'] . '?' . $args;
        }
        if ($access_token && is_array($access_token)) {
            throw new AppException($access_token['errmsg'], isset($access_token['errcode']) ? $access_token['errcode'] : '0');
        }
        throw new NoAccessTokenException();
    }

    /**
     * 执行Weixin Api GET 方式 ，不用带access_token 参数<br/>
     * 如果获取access_token 失败 ，抛出异常
     *
     * @param string|array $url
     * @param boolean $refreshToken
     *            是否刷新access_token
     * @throws NoAccessTokenException|AppException
     * @return array 返回执行结果
     */
    public function getData($url, $refreshToken = false)
    {
        if(is_string($url)){
            $curl = $this->processAccessToken($url, $refreshToken);
            $result = Http::get($curl);

            if (isset($result['errcode']) && ($result['errcode'] == '42001' || $result['errcode'] == '40001')) {
                return $this->getData($url, true);
            }
            return $result;
        }
        if(is_array($url)){
            foreach ($url as $key=>$urlInfo){
                $curl = $this->processAccessToken($urlInfo['url'], $refreshToken);
                $url[$key]['url']=$curl;
            }
            $results=Http::mutilGet($url);
            foreach ($results as $key=>$result){
                $results[$key]['result']=@json_decode($result['result'],true);
            }
            return $results;
        }
        return ['errcode'=>1,'errmsg'=>'url type is error'];
    }

    /**
     * 执行Weixin Api POST 方式 ，不用带access_token 参数<br/>
     * 如果获取access_token 失败 ，抛出异常
     *
     * @param string|array $url
     * @param string|array $params
     * @param boolean $refreshToken
     *            是否刷新access_token
     * @throws NoAccessTokenException|AppException
     * @return array 返回执行结果
     */
    public function postData($url, $params=array(), $refreshToken = false)
    {
        if(is_string($url)){
            $curl = $this->processAccessToken($url, $refreshToken);
            $result = Http::post($curl, $params);
            if (isset($result['errcode']) && ($result['errcode'] == '42001' || $result['errcode'] == '40001')) {
                return $this->postData($url, $params, true);
            }
            return $result;
        }
        if(is_array($url)){
            foreach ($url as $key=>$urlInfo){
                $curl = $this->processAccessToken($urlInfo['url'], $refreshToken);
                $url[$key]['url']=$curl;
            }
            $results=Http::mutilPost($url);
            foreach ($results as $key=>$result){
                $results[$key]['result']=@json_decode($result['result'],true);
            }
            return $results;
        }
        return ['errcode'=>1,'errmsg'=>'url type is error'];
    }

    /**
     * @param array $params
     * @param bool $refreshToken
     * @return array
     */
    public function createQrcode(array $params,$refreshToken=false){
        return array($params,$refreshToken);
    }
}