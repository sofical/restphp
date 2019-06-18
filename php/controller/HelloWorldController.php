<?php
namespace php\controller;
use restphp\http\RestHttpResponse;

/**
 * Created by zj.
 * User: zj
 * Date: 2019/6/18 0018
 * Time: 下午 1:44
 */
class HelloWorldController {
    #RequestMapping(value="/hello", method="GET")
    public function helloGET() {
        RestHttpResponse::html("Hello world!");
    }

    #RequestMapping(value="/hello", method="POST")
    public function helloPOST() {
        RestHttpResponse::json(array(
            "data" => "Hello world!"
        ));
    }
}