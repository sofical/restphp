<?php
namespace restphp\core;

/**
 * Class RestConstant
 * @author sofical
 * @date 2017-03-17
 * @package restphp\core
 */
class RestConstant {
    public static function REST_TARGET() {
        return DIR_RESTPHP . DIRECTORY_SEPARATOR . 'target';
    }

    public static function REST_URI_ALL_END() {
        return 'RESTPHPURIALLEND';
    }

    public static function REST_URI_SIGE_END() {
        return 'RESTPHPURISIGEEND';
    }
}