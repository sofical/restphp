<?php
namespace restphp\exception;
use restphp\biz\RestErrorCode;
use restphp\http\RestHttpResponse;
use restphp\http\RestHttpStatus;
use restphp\i18n\RestLangUtils;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/24 0024
 * Time: 上午 11:07
 */
class RestHtmlException extends \Exception {
    public function __construct($strErrorMessage, $intHttpStatus = RestHttpStatus::Bad_Request,
                                $arrReplaceParam = array()) {
        $strErrorMessage = RestLangUtils::replace($strErrorMessage);
        $strErrorMessage = vsprintf($strErrorMessage, $arrReplaceParam);
        RestHttpResponse::htmlErr($strErrorMessage, $intHttpStatus);
    }
}