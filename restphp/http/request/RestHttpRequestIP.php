<?php
namespace restphp\http\request;

use restphp\http\RestHttpRequest;

/**
 * Class RestHttpRequestIP
 * @author sofical
 * @package restphp\http\request
 */
final class RestHttpRequestIP {
    /**
     * 获取客户IP
     * @return string
     */
    public static function getAgentIp() {
        if (RestHttpRequest::getServer("HTTP_X_FORWARDED_FOR") != null)
            $strIp = RestHttpRequest::getServer("HTTP_X_FORWARDED_FOR");
        else if (RestHttpRequest::getServer("HTTP_CLIENT_IP") != null)
            $strIp = RestHttpRequest::getServer("HTTP_CLIENT_IP");
        else if (RestHttpRequest::getServer("REMOTE_ADDR") != null)
            $strIp = RestHttpRequest::getServer("REMOTE_ADDR");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $strIp = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("HTTP_CLIENT_IP"))
            $strIp = getenv("HTTP_CLIENT_IP");
        else if (getenv("REMOTE_ADDR"))
            $strIp = getenv("REMOTE_ADDR");
        else
            $strIp = "";
        return $strIp;
    }

    /**
     * 是否为IPV4
     * @param string $paR_strIp
     * @return boolean
     */
    public static function isIPV4($strIp){
       /* $bRet=false;
        $tmpStr = $strIp;
        if(pp_char::isnull($tmpStr)) return $ret;
        $tmpStr=explode(".", $tmpStr);
        if(!is_array($tmpStr)) return $ret;
        for($tmpLoop =0; $tmpLoop < count($tmpStr); $tmpLoop++){
            if(pp_char::isnull($tmpStr[$tmpLoop])) return $ret;
            if(!is_numeric($tmpStr[$tmpLoop])) return $ret;
            if(strlen($tmpStr[$tmpLoop])>3) return $ret;
            if((int)($tmpStr[$tmpLoop]) > 255 || (int)($tmpStr[$tmpLoop])<0) return $ret;
        }
        return true;*/
    }
}