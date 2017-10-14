<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/10
 * Time: 下午7:03
 */

namespace Except;


use Exception;

class NotFind extends \Exception
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        header("HTTP/1.0 404 Not Found");
    }
}