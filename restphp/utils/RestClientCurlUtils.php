<?php
namespace restphp\utils;
use restphp\exception\RestException;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/23 0023
 * Time: 下午 6:01
 */
class RestClientCurlUtils {
    public static function get($url, $arrHeader = array(), $intTimeout = 30){
        if(function_exists('curl_init')){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER,self::__formatHeader($arrHeader));
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $data = curl_exec($ch);
            curl_close($ch);
        }else{
            $data = RestClientFileGetContentUtils::get($url, $intTimeout, $arrHeader);
        }
        return $data;
    }

    public static function post($url, $query, $arrHeader = array(), $inTimeout = 30){
        if(function_exists('curl_init')){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER,self::__formatHeader($arrHeader));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $data = curl_exec($ch);
            curl_close($ch);
        }else{
            $data = RestClientFileGetContentUtils::post($url, $query, $inTimeout, $arrHeader);
        }
        return $data;
    }

    public static function getV2($url, $arrHeader = array(), $intTimeout = 30) {
        if(function_exists('curl_init')){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER,self::__formatHeader($arrHeader));
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_HEADER, true);
            $data = curl_exec($ch);
            $arrResult = self::formatV2Result($ch, $data);
            curl_close($ch);
            return $arrResult;
        }else{
            throw new RestException("Fatal error: not support curl");
        }
    }

    public static function postV2($url, $query, $arrHeader = array(), $inTimeout = 30){
        $strRequestData = "";
        if (is_array($query) && !empty($query)) {
            foreach ($query as $key=>$value) {
                $strRequestData .= (""==$strRequestData ? "" : "&") . $key . "=" . urlencode($value);
            }
        } else if (is_string($query)) {
            $strRequestData = $query;
        }
        if(function_exists('curl_init')){
            $ch = curl_init();
            $arrHeaderFinal = self::__formatHeader($arrHeader);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeaderFinal);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $strRequestData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_HEADER, true);
            $data = curl_exec($ch);
            $arrResult = self::formatV2Result($ch, $data);
            curl_close($ch);
            return $arrResult;
        }else{
            throw new RestException("Fatal error: not support curl");
        }
        return $data;
    }

    private static function formatV2Result($ch, $data) {
        $intHeaderSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $strHeader = substr($data, 0, $intHeaderSize);
        $arrHeaderSource = explode("\n", $strHeader);
        $arrHeader = array();
        $intStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $protocol = "";
        foreach ($arrHeaderSource as $strHeaderItem) {
            if (strtoupper(substr($strHeaderItem,0, 5)) == "HTTP/") {
                $protocol = $strHeaderItem;
                continue;
            }
            $strHeaderItem = trim($strHeaderItem);
            $arrHeader[] = $strHeaderItem;
        }

        $strBody = substr($data, $intHeaderSize);

        $arrData = array(
            'protocol' => $protocol,
            'status' => $intStatus,
            'header' => $arrHeader,
            'body' => $strBody
        );

        return $arrData;
    }

    private static function __formatHeader($arrHeader) {
        $arrNew = array();
        foreach ($arrHeader as $strKey=>$strValue)  {
            $strKey = str_replace("HTTP_", "", $strKey);
            $arrNew[] = $strKey.":".$strValue;
        }
        return $arrNew;
    }
}