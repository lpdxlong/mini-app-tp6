<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-06-25
 * Time: 11:50
 */

namespace ooyyee\miniapp\exception;


class NoAccessTokenException extends \Exception
{
    public function __construct()
    {
        parent::__construct('accessToken 不存在', 0);
    }
}