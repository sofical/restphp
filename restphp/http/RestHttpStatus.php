<?php
namespace restphp\http;

/**
 * Class RestHttpStatus
 * @author sofical
 * @date 2017-03-17
 * @package restphp\http
 */
final class RestHttpStatus {
    public static function statusReson($strStatus) {
        return isset(self::$_arrStartusReson[$strStatus]) ? self::$_arrStartusReson[$strStatus] : '';
    }

    public static function arrStatusReson() {
        return self::$_arrStartusReson;
    }

    private static $_arrStartusReson = array(
        '100' => 'Continue',
        '101' => 'Switching Protocols',
        '102' => 'Processing',
        '200' => 'OK',
        '201' => 'Created',
        '202' => 'Accepted',
        '203' => 'Non-Authoritative Information',
        '204' => 'No Content',
        '205' => 'Reset Content',
        '206' => 'Partial Content',
        '207' => 'Multi-Status',
        '300' => 'Multiple Choices',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '303' => 'See Other',
        '304' => 'Not Modified',
        '305' => 'Use Proxy',
        '307' => 'Temporary Redirect',
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '402' => 'Payment Required',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method Not Allowed',
        '406' => 'Not Acceptable',
        '407' => 'Proxy Authentication Required',
        '408' => 'Request Time-out',
        '409' => 'Conflict',
        '410' => 'Gone',
        '411' => 'Length Required',
        '412' => 'Precondition Failed',
        '413' => 'Request Entity Too Large',
        '414' => 'Request-URI Too Large',
        '415' => 'Unsupported Media Type',
        '416' => 'Requested range not satisfiable',
        '417' => 'Expectation Failed',
        '421' => 'There are too many connections from your internet address',
        '422' => 'Unprocessable Entity',
        '423' => 'Locked',
        '424' => 'Failed Dependency',
        '425' => 'Unordered Collection',
        '426' => 'Upgrade Required',
        '449' => 'Retry With',
        '501' => 'Internal Server Error',
        '502' => 'Not Implemented',
        '503' => 'Service Unavailable',
        '504' => 'Gateway Time-out',
        '505' => 'HTTP Version not supported',
        '506' => 'Variant Also Negotiates',
        '507' => 'Insufficient Storage',
        '509' => 'Bandwidth Limit Exceeded',
        '510' => 'Not Extended',
        '600' => 'Unparseable Response Headers'
    );
}