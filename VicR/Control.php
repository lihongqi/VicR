<?php

/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/31
 * Time: 下午3:52
 */
class Control
{

    protected $result = [
        'err' => 0, //错误码
        'msg' => '', //错误提示
        'res' => []  //返回的数据
    ];

    protected $base_tpl = '';

    protected $page_size = 20;

    public function __construct()
    {

    }

    protected function getLimit($max_page = 100)
    {
        $page = \Request::get('page', 1);
        $page = min($page, $max_page);
        return ($page - 1) * $this->page_size . ',' . $this->page_size;
    }


    public function __destruct()
    {
//        Log::debug('time:' . ceil(((microtime(true) - App::$start_time) * 1000)) . 'ms');
    }

    protected function err($msg, $code)
    {
        $this->result['err'] = $code;
        $this->result['msg'] = $msg;
        return json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $data
     */
    protected function json($data)
    {
        $this->result['res'] = $data;
        return json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param array $data
     * @param string $callback
     */
    protected function jsonp($data, $callback = 'callback')
    {
        return $callback . '(' . $this->json($data) . ');';
    }

    /**
     * @param array $fields
     * @param array $data
     */
    protected function verify($fields, $data)
    {
        foreach ($fields as $k => $v) {
            if (is_numeric($k)) {
                $k = $v;
            }
            $val = Funcs::array_get($data, $k);
            if ($val == null && $val == '') {
                throw (new \Except\Error("{$v}不能为空", 4001))->setTime(5)->back();
            }
        }
    }

    /**
     * @param $tpl
     * @param array $data
     * @return string
     */
    protected function baseTpl($tpl, $data = [])
    {
        $data['tpl'] = $tpl;
        return \Response::tpl($this->base_tpl, $data);
    }

}