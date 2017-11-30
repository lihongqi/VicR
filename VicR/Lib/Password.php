<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/11/1
 * Time: 下午3:58
 */

namespace Lib;


class Password
{
    const AES_METHOD = 'AES-128-ECB';

    private $secret_key = '';

    private $sign_key = '';

    /**
     * @param $str
     * @return bool|string
     */
    public static function hash($str){
        return password_hash($str, PASSWORD_DEFAULT);
    }

    /**
     * @param $str
     * @param $hash
     * @return bool
     */
    public static function verify($str,$hash){
        return password_verify($str, $hash);
    }

    /**
     * Crypt constructor.
     * @param string $secret_key
     * @param string $sign_key
     */
    public function __construct($secret_key,$sign_key){
        $this->secret_key = $secret_key;
        $this->sign_key = $sign_key;
    }

    /**
     * 解密
     * @param $secretData
     * @return string
     */
    public function decode($secretData){
        return openssl_decrypt($secretData, self::AES_METHOD, $this->secret_key, false);
    }

    /**
     * 加密
     * @param $data
     * @return string
     */
    public function encode($data){
        return openssl_encrypt($data, self::AES_METHOD, $this->secret_key, false);
    }

    /**
     * 签名
     * @param $data
     * @return string
     */
    public function sign($data){
        return md5($data.$this->sign_key);
    }

    /**
     * 检测签名
     * @param $data
     * @param $sign
     * @return bool
     */
    public function checkSign($data,$sign){
        return $this->sign($data) == $sign;
    }
}