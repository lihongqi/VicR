<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/31
 * Time: 下午8:26
 */

namespace Except;


use Exception;

class Base extends Exception
{
    protected $url = '';

    protected $time = 5;

    protected $is_json = 0;

    protected $type = 'error';

    protected $back_url = 0;

    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    public function back()
    {
        $this->back_url = 1;
        return $this;
    }

    public function json()
    {
        $this->is_json = 1;
        return $this;
    }

    public function __destruct()
    {
        if ($this->is_json || \Request::isAjax()) {
            echo json_encode([
                'err' => $this->code,
                'msg' => $this->message,
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo \Response::tpl('Except/index', [
                'msg' => $this->message,
                'time' => $this->time,
                'url' => $this->url,
                'back' => $this->back_url,
                'type' => $this->type
            ]);

        }
    }

}