<?php
namespace restphp\http;

use restphp\biz\RestContentType;
use restphp\biz\RestErrorCode;
use restphp\utils\RestUUIDUtil;
use restphp\utils\RestXmlUtil;

/**
 * Class RestHttpResponse
 * @author sofical
 * @date 2017-03-17
 * @package restphp\http
 */
final class RestHttpResponse{
    /**
     * 普通输出
     * @param $mxResponse
     * @param string $strHttpStatus
     * @param array $arrHeader
     */
    public static function response($mxResponse, $strHttpStatus = '200', $arrHeader = array()) {
        switch(CONTENT_TYPE) {
            case RestContentType::HTML:
                self::html($mxResponse, $strHttpStatus, $arrHeader);
                break;
            case RestContentType::XML:
                self::xml($mxResponse, $strHttpStatus, $arrHeader);
                break;
            case RestContentType::JSON:
            default:
                self::json($mxResponse, $strHttpStatus, $arrHeader);
                break;
        }
    }

    /**
     * 错误输出
     * @param $strMsg
     * @param string $strCode
     * @param string $strHttpStatus
     * @param array $arrHeader
     */
    public static function err($strMsg, $strCode = RestErrorCode::INVALID_ARGUMENT, $strHttpStatus = '400', $arrHeader = array()) {
        switch(CONTENT_TYPE) {
            case RestContentType::XML:
                self::xmlErr($strMsg, $strCode, $strHttpStatus, $arrHeader);
                break;
            case RestContentType::JSON:
            default:
                self::jsonErr($strMsg, $strCode, $strHttpStatus, $arrHeader);
                break;
        }
    }

    public static function html($strResponseBody, $strHttpStatus = '200', $arrHeader = array()) {
        $arrHeader['Content-Type'] = RestContentType::HTML;
        self::_output($strHttpStatus, $arrHeader, $strResponseBody);
    }

    public static function json($arrResponseBody, $strHttpStatus = '200', $arrHeader = array()) {
        $arrHeader['Content-Type'] = RestContentType::JSON;
        self::_output($strHttpStatus, json_encode($arrResponseBody), $arrHeader);
    }

    public static function xml($arrResponseBody, $strHttpStatus = '200', $arrHeader = array()) {
        $arrHeader['Content-Type'] = RestContentType::XML;
        self::_output($strHttpStatus, RestXmlUtil::arrToXml($arrResponseBody), $arrHeader);
    }

    public static function htmlErr($strErrorReason, $strHttpStatus = '400', $arrHeader = array()) {
        $arrHeader['Content-Type'] = RestContentType::HTML;
        self::_output($strHttpStatus, $strErrorReason, $arrHeader);
        die();
    }

    public static function jsonErr($strErrorReason, $strCode = RestErrorCode::INVALID_ARGUMENT, $strHttpStatus = '400' , $arrHeader = array()) {
        $arrHeader['Content-Type'] = RestContentType::JSON;
        $arrResponseBody = array(
            'host' => RestHttpRequest::getServer('HTTP_HOST'),
            'code' => $strCode,
            'message' => $strErrorReason,
            'server_time' => SYS_MICRO_TIME,
            'id' => RestUUIDUtil::guid()
        );
        self::_output($strHttpStatus, $arrHeader, json_encode($arrResponseBody));
        die();
    }

    public static function xmlErr($strErrorReason, $strCode = RestErrorCode::INVALID_ARGUMENT, $strHttpStatus = '400', $arrHeader = array()) {
        $arrHeader['Content-Type'] = RestContentType::XML;
        $arrResponseBody = array(
            'host' => RestHttpRequest::getServer('HTTP_HOST'),
            'code' => $strCode,
            'message' => $strErrorReason,
            'server_time' => SYS_MICRO_TIME,
            'id' => RestUUIDUtil::guid()
        );
        self::_output($strHttpStatus, $arrHeader, RestXmlUtil::arrToXml($arrResponseBody));
        die();
    }

    private static function _output($strHttpStatus, $arrHeader, $strResponseBody = '') {
        header('HTTP/' . HTTP_VERSION . ' ' .$strHttpStatus . ' ' .RestHttpStatus::statusReson($strHttpStatus));
        if (!empty($arrHeader)) {
            foreach($arrHeader as $name=>$val) {
                header($name . ':' . $val);
            }
        }
        echo $strResponseBody;
    }
}