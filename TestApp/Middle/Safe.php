<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 2017/12/14
 * Time: 下午2:36
 */

namespace Middle;


use Except\Error;

class Safe
{
    public static function csrfSetSign($next)
    {
        $str = $next();
        // add sign
        if(!isset($_SESSION['_csrf_'])){
            $_SESSION['_csrf_'] = \Funcs::uuid();
        }
        $str = str_replace('</form>','<input type="hidden" name="_csrf_" value="'.$_SESSION['_csrf_'].'"></form>',$str);
        return $str;
    }

    public static function csrfVerifySign($next)
    {
        $s = \Request::res('_csrf_');
        if($s && $s == $_SESSION['_csrf_']){
            return $next();
        }else{
            throw (new Error('非法请求',403))->back();
        }
    }
}