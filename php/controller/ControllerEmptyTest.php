<?php
namespace php\controller;

#RequestMapping(value="")
class ControllerEmptyTest{
    #RequestMapping(value="", method="GET")
    public function index() {
        echo '欢迎使用 RestPHP ' . REST_PHP_VERSION . '!';
    }
}