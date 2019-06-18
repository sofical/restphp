<?php
/**
 * Created by zj.
 * User: zj
 * Date: 2019/5/29 0029
 * Time: 上午 9:54
 */

namespace restphp\utils;

use restphp\http\RestHttpRequest;

class RestHttpRequestUtils {
    /**
     * 获取当前主机头.
     * @return string
     */
    public static function getHost() {
        $strServerName = RestHttpRequest::getServer("SERVER_NAME");
        $strPort = RestHttpRequest::getServer("SERVER_PORT");
        return $strServerName . ("80" == $strPort ? "" : ":{$strPort}");
    }

    /**
     * 获取uri.
     */
    public static function getUri() {
        return RestHttpRequest::getServer("REQUEST_URI");
    }

    /**
     * 获取http method
     */
    public static function getMethod() {
        return RestHttpRequest::getServer("REQUEST_METHOD");
    }
}