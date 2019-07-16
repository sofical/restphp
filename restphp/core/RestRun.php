<?php
namespace restphp\core;

use restphp\http\RestHttpRequest;
use restphp\http\RestHttpResponse;
use restphp\biz\RestErrorCode;

/**
 * 路由控制
 * @author sofical
 * @date 2017-03-17
 * @package restphp\core
 */
class RestRun{
    public static function run() {
        //Http请求数据预处理
        RestHttpRequest::init();

        $strMethod = RestHttpRequest::getServer('REQUEST_METHOD');
        if ($strMethod == null) {
            self::_unknownMethod();
        }
        $strMethod = strtoupper($strMethod);

        //路由匹配
        $strUri = '/';
        if (RestHttpRequest::getServer('REQUEST_URI') != null) {
            $strUri = RestHttpRequest::getServer('REQUEST_URI');
        }
        if (RestHttpRequest::getServer('HTTP_X_ORIGINAL_URL') != null) {
            $strUri = RestHttpRequest::getServer('HTTP_X_ORIGINAL_URL');
        }

        $nUrlParamPOS = strpos($strUri, "?");
        $strUri = strpos($strUri, "?") > -1 ? substr($strUri, 0, $nUrlParamPOS) : $strUri;

        $strMapFile = RestConstant::REST_TARGET() . DIRECTORY_SEPARATOR . $strMethod . '.php';
        $arrMap = array();
        if (file_exists($strMapFile)) {
            $arrMap = include($strMapFile);
        } else {
            self::_notFound(array('method' => $strMethod, 'uri' => $strUri));
        }

        $strUri = str_replace('//', '/', $strUri);
        if (substr($strUri, strlen($strUri)-1, 1) == '/') {
            substr($strUri, 0, strlen($strUri)-1);
        }
        $strUriKey = str_replace('/', '_', $strUri);

        if (isset($arrMap[$strUriKey])) {
            $strFileEnter = RestConstant::REST_TARGET() . DIRECTORY_SEPARATOR .
                $strMethod . DIRECTORY_SEPARATOR . $arrMap[$strUriKey]['filename'] . ".php";
            include($strFileEnter);
            return;
        } else {
            foreach($arrMap as $strKey => $arrMatchedMap) {
                if ($strKey == '_') {
                    continue;
                }
                $strR = '/' . $arrMatchedMap['preg_match'] . '/';
                $strM = $strUri . '/' . RestConstant::REST_URI_SIGE_END();
                preg_match($strR, $strM, $arrMatched);
                if (isset($arrMatched[0])) {
                    $arrPathParam = $arrMatchedMap['path_param'];
                    for($nPos = 1; $nPos < count($arrMatched); $nPos++) {
                        $strPathVal = str_replace('/', '', $arrMatched[$nPos]);
                        RestHttpRequest::setPathValue($arrPathParam[$nPos-1], $strPathVal);
                    }
                    $strFileEnter = RestConstant::REST_TARGET() . DIRECTORY_SEPARATOR .
                        $strMethod . DIRECTORY_SEPARATOR . $arrMatchedMap['filename'] . ".php";
                    include($strFileEnter);
                    return;
                }
            }
            foreach($arrMap as $strKey => $arrMatchedMap) {
                if ($strKey == '_') {
                    continue;
                }
                $strR = '/' . $arrMatchedMap['preg_match'] . '/';
                $strM = $strUri . '/' . RestConstant::REST_URI_ALL_END();
                preg_match($strR, $strM, $arrMatched);
                if (isset($arrMatched[0])) {
                    $strReCheck = $arrMatched[count($arrMatched) - 1];
                    if (substr($strReCheck, strlen($strReCheck) - 1) == '/') {
                        $strReCheck = substr($strReCheck, 0, strlen($strReCheck)-1);
                    }
                    if ( strpos($strReCheck, '/') > -1) {
                        continue;
                    }
                    $arrPathParam = $arrMatchedMap['path_param'];
                    for($nPos = 1; $nPos < count($arrMatched); $nPos++) {
                        $strPathVal = str_replace('/', '', $arrMatched[$nPos]);
                        RestHttpRequest::setPathValue($arrPathParam[$nPos-1], $strPathVal);
                    }
                    $strFileEnter = RestConstant::REST_TARGET() . DIRECTORY_SEPARATOR .
                        $strMethod . DIRECTORY_SEPARATOR . $arrMatchedMap['filename'] . ".php";
                    include($strFileEnter);
                    return;
                }
            }
        }

        self::_notFound(array('method' => $strMethod, 'uri' => $strUri));
    }

    private static function _notFound($arrArgs) {
        $strResponseBody = "the method '[" . $arrArgs['method'] . "'] of '" . $arrArgs['uri'] . "' is not Found!";
        RestHttpResponse::err($strResponseBody, RestErrorCode::URI_NOT_FOUND, '404');
    }

    private static function _unknownMethod() {
        $strResponseBody = 'method missed';
        RestHttpResponse::err($strResponseBody, RestErrorCode::UNKNOWN_METHOD, '405');
    }
}