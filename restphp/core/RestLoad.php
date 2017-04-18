<?php
namespace restphp\core;

/**
 * 自动加载
 * @author sofical
 * @date 2017-03-17
 * @package restphp\core
 */
class RestLoad {
    public static function load($strName) {
        self::$arrMap[] = array(
            'tag' => 'restphp',
            'dir' => (DIR_RESTPHP . DIRECTORY_SEPARATOR)
        );
        self::_loadRestphp($strName) or die('cloud not load class:' . $strName);
    }

    private static $arrMap = array(
    );

    public static function _loadRestphp($strName) {
        foreach (self::$arrMap as $arrRoute) {
            if (substr($strName, 0, strlen($arrRoute['tag'])) == $arrRoute['tag']) {
                include_once($arrRoute['dir'].substr($strName, strlen($arrRoute['tag'])) . '.php');
                return true;
            }
        }
        $strMaybe = $strName . '.php';
        if (file_exists($strMaybe)) {
            include_once($strMaybe);
            return true;
        }
        return false;
    }
}