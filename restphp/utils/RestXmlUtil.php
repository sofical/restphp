<?php
namespace restphp\utils;

/**
 * Class RestXmlUtil
 * @author sofical
 * @date 2017-03-17
 * @package restphp\utils
 */
final class RestXmlUtil {
    /**
     * 数组转XML
     * @param $arrParams
     * @param string $strTag
     * @param bool|false $bNeedHead
     * @param string $strVersion
     * @param string $strEncodeing
     * @return string
     */
    public static function arrToXml($arrParams, $strTag='body', $bNeedHead = false, $strVersion = '1.0', $strEncodeing = 'utf-8'){
        $strXml = $bNeedHead ? "<?xml version=\"{$strVersion}\" encoding=\"{$strEncodeing}\" ?>" : "";
        $strXml .= '<' . $strTag;
        $arrSub = array();
        $bParent = true;
        foreach($arrParams as $strKey => $mxValue){
            if(is_array($mxValue)){
                $arrSub[$strKey] = $mxValue;
            }else{
                $strXml .= " {$strKey}=\"{$mxValue}\" ";
                $bParent=false;
            }
        }
        $strXml .= empty($arrSub) ? " />" : ">";
        if($bParent && !empty($arrSub)){
            $strXml = "";
        }

        if(!empty($arrSub)){
            foreach($arrSub as $mxKey=>$mxValue){
                if(!is_numeric($mxKey)){
                    if($bParent) $strXml .= "<{$strTag}>";
                    $strXml .= self::arrToXml($mxValue, $mxKey);
                    if($bParent) $strXml .= "</{$strTag}>";
                }else{
                    $strXml .= self::arrToXml($mxValue, $bParent ? $strTag : $mxKey);
                }
            }
        }

        if(!($bParent && !empty($arrSub))){
            $strXml .= empty($arrSub) ? "" : "</{$strTag}>";
        }
        return $strXml;
    }

    /**
     * XML转数组
     * @author sofical
     * @date 2017-03-17
     * @param $strXml
     * @return array
     */
    public static function xmlToArr($strXml){
        $obj = @simplexml_load_string($strXml);
        $arrReturn = array();
        if($obj){
            $arrReturn = self::objToArr($obj);
        }
        return $arrReturn;
    }

    /**
     * 对象转数组
     * @author sofical
     * @date 2017-03-17
     * @param $obj
     * @return array
     */
    public static function objToArr($obj){
        $array = array();
        if(is_object($obj)){
            $array =  (array)($obj);
            foreach ($array as $strKey=>$mxValue){
                if($strKey == '@attributes'){
                    foreach($mxValue as $strSubKey => $mxSubValue){
                        $array[$strSubKey] = $mxSubValue;
                    }
                    unset($array['@attributes']);
                }else{
                    if(is_object($mxValue)){
                        $array[$strKey] = self::objToArr($mxValue);
                    }else{
                        $array[$strKey] = $mxValue;
                    }
                }
            }
        }
        return $array;
    }
}