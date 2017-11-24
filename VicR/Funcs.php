<?php

/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/9
 * Time: 下午4:01
 */
class Funcs
{
    /**
     * @param array $arr
     * @param $key
     * @return mixed|null
     */
    public static function array_get($arr, $key)
    {
        if (isset($arr[$key])) {
            return $arr[$key];
        } else if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            foreach ($keys as $v) {
                if (isset($arr[$v])) {
                    $arr = $arr[$v];
                } else {
                    return null;
                }
            }
            return $arr;
        } else {
            return null;
        }
    }


    /**
     * @param array $arr
     * @param array $keys
     * @return mixed|null
     */
    public static function array_get_not_null($arr, $keys)
    {
        foreach ($keys as $v) {
            if (self::array_get($arr, $v) !== null) {
                return self::array_get($arr, $v);
            }
        }
        return null;
    }

    /**
     * uuid生成 php7+
     * @param string $prefix
     * @return string
     */
    public static function uuid($prefix = ''){
        $str = uniqid('',true);
        $arr = explode('.',$str);
        $str = $prefix.base_convert($arr[0],16,36).base_convert($arr[1],10,36).base_convert(bin2hex(random_bytes(5)),16,36);
        $len = 24;
        $str = substr($str,0,$len);
        if(strlen($str) < $len){
            $mt = base_convert(bin2hex(random_bytes(5)),16,36);
            $str = $str.substr($mt,0,$len - strlen($str));
        }
        return $str;
    }


    /**
     * @param $str
     * @param null $allow_tags
     * @return string
     */
    public static function filterXss($str,$allow_tags = null){
        $str=strip_tags($str,$allow_tags);
        if($allow_tags !== null){
            while (true){
                $l = strlen($str);
                $str = preg_replace('/(<[^>]+?)(on[a-z]+)([^<>]+>)/i','$1$3',$str);
                $str = preg_replace('/(<[^>]+?)(javascript\:)([^<>]+>)/i','$1$3',$str);
                if(strlen($str) == $l){
                    break ;
                }
            }
        }
        return $str;
    }

    /**
     * @param int $total_row 总记录数量
     * @param int $page_row 每页显示条数
     * @param int $show_page 显示分页数
     * @param int $page 当前页面数
     * @param array $temp
     * @param int $show_page_max
     * @return string
     */
    public static function page($total_row,$page_row,$show_page,$page = 1,$temp=[],$show_page_max=100){
        if(empty($temp)){
            $url=Request::uri().'?page=';
            $temp=array(
                '<a href="'.$url.'{i}" class="pure-button">上一页</a>',
                '<a class="pure-button pure-button-active">{i}</a>',
                '<a href="'.$url.'{i}" class="pure-button">下一页</a>',
                '<a href="'.$url.'{i}" class="pure-button">{i}</a>'
            );
        }
        $s='';
        $pages=ceil($total_row/$page_row);
        if($show_page && $pages >$show_page_max){
            $pages=$show_page_max;
        }
        if($page>1){
            $s.=str_replace('{i}',$page-1,$temp[0]);
        }
        if($page-$show_page > 1){
            $s.=str_replace('{i}',1,$temp[3]).'…';
        }
        $zxid=$page-$show_page<1?1:$page-$show_page;
        for($i=$page;$i>$zxid;$i--){}
        for(;$i<$page;$i++){
            $s.=str_replace('{i}',$i,$temp[3]);
        }
        $s.=str_replace('{i}',$page,$temp[1]);
        $zuid=$page+$show_page>$pages?$pages:$page+$show_page;
        for($i=($page+1);$i<($zuid+1);$i++){
            $s.=str_replace('{i}',$i,$temp[3]);
        }
        if($page+$show_page < $pages){
            $s.='…'.str_replace('{i}',$pages,$temp[3]);
        }
        if($page<$pages){
            $s.=str_replace('{i}',$page+1,$temp[2]);
        }
        return $s;
    }

}