<?php
namespace restphp\utils;

/**
 * 唯一码生成工具类
 * @author sofical
 * @date 2017-03-17
 * @package restphp\utils
 */
final class RestUUIDUtil {
    public static function guid($prefix = '') {
        $strChars = md5(uniqid(microtime(true), true));
        $strUuid  = substr($strChars,0,8) . '-';
        $strUuid .= substr($strChars,8,4) . '-';
        $strUuid .= substr($strChars,12,4) . '-';
        $strUuid .= substr($strChars,16,4) . '-';
        $strUuid .= substr($strChars,20,12);
        return $prefix . $strUuid;
    }
}