<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-06-24
 * Time: 16:17
 */

namespace ooyyee\miniapp\exception;


use Throwable;

class NotSupportException extends \Exception
{
    public function __construct($type , $code = 0, Throwable $previous = null)
    {
        parent::__construct($type.'不支持此类型的小程序', $code, $previous);
    }
}