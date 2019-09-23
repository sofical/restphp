<?php
namespace restphp\exception;
use restphp\biz\RestErrorCode;
use restphp\http\RestHttpResponse;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/24 0024
 * Time: 上午 11:07
 */
class RestJsonException extends \Exception {
    public function __construct($strErrorMessage, $strErrorCode=RestErrorCode::UNDEFINED, $intHttpStatus = 400) {
        RestHttpResponse::jsonErr($strErrorMessage, $strErrorCode, $intHttpStatus);
    }
}