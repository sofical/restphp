<?php
namespace restphp\utils;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/23 0023
 * Time: 下午 6:02
 */
class RestClientFileGetContentUtils {
    /**
     * Get方式抓包
     * @param string $url 目标地址
     * @param integer $timeout 超时时间
     * @param array $header 头部信息Key-Value数组
     * @return string
     */
    public static function get($url, $timeout=3, $header=array()){
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'timeout'=>$timeout,
            )
        );
        if(is_array($header)&&!empty($header)){
            $header_string = "";
            foreach ($header as $key=>$value){
                $header_string.="{$key}:{$value}\r\n";
            }
            $opts['http']['header'] = $header_string;
        }
        $context = stream_context_create($opts);
        $body = file_get_contents($url, false, $context);
        RestLog::writeLog("请求地址【{$url}】，请求context【" . json_encode($context) . "】，响应内容【" .
            $body ."】", "http.f.");
        return $body;
    }

    /**
     * Post方式抓包
     * @param string $url 目录地址
     * @param array $data POST数据Key-Value数组
     * @param integer $timeout 超时时间
     * @param array $header 头部信息Key-Value数组
     * @return string
     */
    public static function post($url, $data=NULL, $timeout=3, $header=array()){
        $opts = array(
            'http'=>array(
                'method'=>"POST",
                'timeout'=>$timeout,
            )
        );
        if(is_array($header)&&!empty($header)){
            $header_string = "";
            foreach ($header as $key=>$value){
                $header_string.="{$key}:{$value}\r\n";
            }
            $opts['http']['header'] = $header_string;
        }
        if(is_array($data)&&!empty($data)){
            $query_string = http_build_query($data);
            $query_lenth = strlen($query_string);
            $opts['http']['header'] = isset($opts['http']['header'])?$opts['http']['header']:'';
            $opts['http']['header'] .= "Content-length:{$query_lenth}\r\n";
            $opts['http']['header'] .= "Content-type: application/x-www-form-urlencoded\r\n";
            $opts['http']['header'] .= "Connection: close";
            $opts['http']['content'] = $query_string;
        }
        $context = stream_context_create($opts);
        $body = file_get_contents($url, false, $context);
        RestLog::writeLog("请求地址【{$url}】，请求context【" .RestStringUtils::isBlank($context)?"": json_encode($context) . "】，响应内容【" .
            $body ."】", "http.f.");
        return $body;
    }
}