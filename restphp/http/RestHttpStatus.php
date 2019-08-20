<?php
namespace restphp\http;

/**
 * Class RestHttpStatus
 * @author sofical
 * @date 2017-03-17
 * @package restphp\http
 */
final class RestHttpStatus {
    const R_Continue= 100;
    const Switching_Protocols = 101;
    const Processing = 102;

    const OK = 200;
    const Created = 201;
    const Accepted = 202;
    const Non_Authoritative_Information = 203;
    const No_Content = 204;
    const Reset_Content = 205;
    const Partial_Content = 206;
    const Multi_Status = 207;

    const Multiple_Choices = 300;
    const Moved_Permanently = 301;
    const Found = 302;
    const See_Other = 303;
    const Not_Modified = 304;
    const Use_Proxy = 305;
    const Temporary_Redirect = 307;

    const Bad_Request = 400;
    const Unauthorized = 401;
    const Payment_Required = 402;
    const Forbidden = 403;
    const Not_Found = 404;
    const Method_Not_Allowed = 405;
    const Not_Acceptable = 406;
    const Proxy_Authentication_Required = 407;
    const Request_Time_out = 408;
    const Conflict = 409;
    const Gone = 410;
    const Length_Required = 411;
    const Precondition_Failed = 412;
    const Request_Entity_Too_Large = 413;
    const Request_URI_Too_Large = 414;
    const Unsupported_Media_Type = 415;
    const Requested_range_not_satisfiable = 416;
    const Expectation_Failed = 417;
    const There_are_too_many_connections_from_your_internet_address = 421;
    const Unprocessable_Entity = 422;
    const Locked = 423;
    const Failed_Dependency = 424;
    const Unordered_Collection = 425;
    const Upgrade_Required = 426;
    const Retry_With = 449;
    const Unavailable_For_Legal_Reasons = 451;

    const Internal_Server_Error = 500;
    const Not_Implemented = 501;
    const Bad_gateway = 502;
    const Service_Unavailable = 503;
    const Gateway_Time_out = 504;
    const HTTP_Version_not_supported = 505;
    const Variant_Also_Negotiates = 506;
    const Insufficient_Storage = 507;
    const Loop_Detected = 508;
    const Bandwidth_Limit_Exceeded = 509;
    const Not_Extended = 510;
    const Network_Authentication_Required = 511;
    const Unparseable_Response_Headers = 600;

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
        '451' => 'Unavailable For Legal Reasons',
        '500' => 'Internal Server Error',
        '501' => 'Not Implemented',
        '502' => 'Bad gateway',
        '503' => 'Service Unavailable',
        '504' => 'Gateway Time-out',
        '505' => 'HTTP Version not supported',
        '506' => 'Variant Also Negotiates',
        '507' => 'Insufficient Storage',
        '508' => 'Loop Detected',
        '509' => 'Bandwidth Limit Exceeded',
        '510' => 'Not Extended',
        '511' => 'Network Authentication Required',
        '600' => 'Unparseable Response Headers'
    );
}