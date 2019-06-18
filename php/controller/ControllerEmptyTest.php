<?php
namespace php\controller;

#RequestMapping(value="")
use restphp\http\RestHttpResponse;

class ControllerEmptyTest{
    #RequestMapping(value="", method="GET")
    public function index() {
        RestHttpResponse::html('欢迎使用 RestPHP ' . REST_PHP_VERSION . '!');
    }
}