<?php
namespace restphp\utils;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/24 0024
 * Time: 上午 9:23
 */
class RestStringUtils {
    /**
     * 是否为邮箱
     * @param string $str
     * @return boolean
     **/
    public static function isEmail($str){
        return preg_match("/^([a-za-z0-9_-])+@([a-za-z0-9_-])+(\.[a-za-z0-9_-])+/",$str);
    }

    /**
     * 是否为英文和字母
     * @param string $str
     * @return boolean
     */
    public static function isEchr($str){
        return preg_match("/^[A-Za-z0-9]+$/", $str);
    }

    public static function isChMobile($str){
        return preg_match("/^1\\d{10}+$/", $str);
    }

    /**
     * 生成随机数
     * @return string
     */
    public static function randomCode() {
        $ranCharArray = array(
            '00' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz',
            '01' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
            '02' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            '03' => 'abcdefghijklmnopqrstuvwxyz',
            '11' => '0123456789',
            '21' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
            '22' => 'abcdefghijklmnopqrstuvwxyz0123456789',
        );

        $argsNum = func_num_args();
        $numeric = 0;
        PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);

        $hash = '';
        switch($argsNum){
            case 1:
                $length = func_get_arg(0);
                if($numeric) {
                    $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
                } else {
                    $hash = '';
                    $chars = $ranCharArray['00'];
                    $max = strlen($chars) - 1;
                    for($i = 0; $i < $length; $i++) {
                        $hash .= $chars[mt_rand(0, $max)];
                    }
                }

                break;
            case 2:
                $length = func_get_arg(0);
                $type = func_get_arg(1);
                if($numeric) {
                    $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
                } else {
                    $hash = '';
                    $chars = $ranCharArray[$type];
                    if(strlen($chars)<10){
                        $hash = self::randomCode($length);
                        return $hash;
                    }else{
                        $max = strlen($chars) - 1;
                        for($i = 0; $i < $length; $i++) {
                            $hash .= $chars[mt_rand(0, $max)];
                        }
                    }
                }

                break;
            default:
                break;
        }

        return $hash;
    }

    /**
     * 是否为utf-8
     * @param string $string
     * @return boolean
     **/
    public static function is_utf8($string){
        return preg_match('%^(?:
				[\x09\x0A\x0D\x20-\x7E] # ASCII
				| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
				| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
				| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
				| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
				| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
				| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
				| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
		)*$%xs', $string);

    }

    /**
     * 判断是否为国标
     * @param string $str
     * @return boolean
     **/
    public static function is_gbk($str){
        if (strlen($str)>=2){
            $str=strtok($str,"");
            if ((ord($str[0])<161) || (ord($str[0])>247)){
                return false;
            }else{
                if((ord($str[1])<161)||(ord($str[1])>254)){
                    return false;
                }else{
                    return true;
                }
            }
        }else{
            return false;
        }
    }

    /**
     * 是否为big
     * @param string $str
     * @return boolean
     **/
    public static function is_big5($str){
        if(strlen($str)>=2){
            $str=strtok($str,"");
            if(ord($str[0]) < 161){
                return false;
            }else{
                if (((ord($str[1]) >= 64)&&(ord($str[1]) <= 126))||((ord($str[1]) >= 161)&&(ord($str[1]) <= 254))){
                    return true;
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
    }

    /**
     * 字符串是否为空.
     * @param $str string 字符串.
     * @return bool
     */
    public static function isBlank($str) {
        return (is_null($str) || "" == $str);
    }
}