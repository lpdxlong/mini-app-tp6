<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-06-24
 * Time: 16:13
 */

namespace ooyyee\miniapp;


class Config
{
    private $appid;
    private $secret;
    private $platform;
    private $token;

    /**
     * @param string $appid
     * @param string $secret
     * @param string $platform
     * @return Config
     */
    public static function create($appid,$secret,$platform){

        $self=new static();
        $self->setAppid($appid);
        $self->setSecret($secret);
        $self->setPlatform($platform);
        return $self;
    }

    /**
     * @param array $app
     * @param string $platform
     * @return Config
     */
    public static function createFromApp($app,$platform){
        $self=new static();
        $self->setAppid($app[$platform.'_appid']);
        $self->setSecret($app[$platform.'_secret']);
        $self->setPlatform($platform);
        return $self;
    }




    /**
     * @return mixed
     */
    public function getAppid()
    {
        return $this->appid;
    }

    /**
     * @return mixed
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }


    /**
     * @param mixed $appid
     */
    public function setAppid($appid)
    {
        $this->appid = $appid;
    }

    /**
     * @param mixed $secret
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param mixed $platform
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;
    }




}