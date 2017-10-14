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
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            foreach ($keys as $v) {
                if (isset($arr[$v])) {
                    $arr = $arr[$v];
                } else {
                    return null;
                }
            }
            return $arr;
        } else if (isset($arr[$key])) {
            return $arr[$key];
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
     * 页面跳转
     * @param $url
     * @param array $args
     */
    public static function redirect($url, $args = [])
    {
        if (isset($args['time'])) {
            header('Refresh:' . $args['time'] . ';url=' . $url);
        } else if (isset($args['httpCode'])) {
            header('Location:' . $url, true, $args['httpCode']);
        } else {
            header('Location:' . $url, true, 302);
        }
    }

    /**
     * @param $str
     * @param null $allowable_tags
     * @return string
     */
    public static function filterXss($str,$allow_tags = null){
        $str=strip_tags($str,$allow_tags);
        if($allow_tags !== null){
            $event = ['onclick', 'oncontextmenu', 'ondblclick', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseover', 'onmouseout', 'onmouseup', 'onkeydown', 'onkeypress', 'onkeyup', 'onabort', 'onbeforeunload', 'onerror', 'onhashchange', 'onload', 'onpageshow', 'onpagehide', 'onresize', 'onscroll', 'onunload', 'onblur', 'onchange', 'onfocus', 'onfocusin', 'onfocusout', 'oninput', 'oninvalid', 'onreset', 'onsearch', 'onselect', 'onsubmit', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'oncopy', 'oncut', 'onpaste', 'onafterprint', 'onbeforeprint', 'onabort', 'oncanplay', 'oncanplaythrough', 'ondurationchange', 'onemptied', 'onended', 'onerror', 'onloadeddata', 'onloadedmetadata', 'onloadstart', 'onpause', 'onplay', 'onplaying', 'onprogress', 'onratechange', 'onseeked', 'onseeking', 'onstalled', 'onsuspend', 'ontimeupdate', 'onvolumechange', 'onwaiting', 'onerror', 'onmessage', 'onopen', 'onmessage', 'onmousewheel', 'ononline', 'onoffline', 'onpopstate', 'onshow', 'onstorage', 'ontoggle', 'onwheel', 'ontouchcancel', 'ontouchend', 'ontouchmove', 'ontouchstart'];
            $event_str = ['0nclick', '0ncontextmenu', '0ndblclick', '0nmousedown', '0nmouseenter', '0nmouseleave', '0nmousemove', '0nmouseover', '0nmouseout', '0nmouseup', '0nkeydown', '0nkeypress', '0nkeyup', '0nabort', '0nbeforeunload', '0nerror', '0nhashchange', '0nload', '0npageshow', '0npagehide', '0nresize', '0nscroll', '0nunload', '0nblur', '0nchange', '0nfocus', '0nfocusin', '0nfocusout', '0ninput', '0ninvalid', '0nreset', '0nsearch', '0nselect', '0nsubmit', '0ndrag', '0ndragend', '0ndragenter', '0ndragleave', '0ndragover', '0ndragstart', '0ndrop', '0ncopy', '0ncut', '0npaste', '0nafterprint', '0nbeforeprint', '0nabort', '0ncanplay', '0ncanplaythrough', '0ndurationchange', '0nemptied', '0nended', '0nerror', '0nloadeddata', '0nloadedmetadata', '0nloadstart', '0npause', '0nplay', '0nplaying', '0nprogress', '0nratechange', '0nseeked', '0nseeking', '0nstalled', '0nsuspend', '0ntimeupdate', '0nvolumechange', '0nwaiting', '0nerror', '0nmessage', '0nopen', '0nmessage', '0nmousewheel', '0nonline', '0noffline', '0npopstate', '0nshow', '0nstorage', '0ntoggle', '0nwheel', '0ntouchcancel', '0ntouchend', '0ntouchmove', '0ntouchstart'];
            $str = str_ireplace($event,$event_str,$str);
        }
        return $str;
    }

}