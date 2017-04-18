<?php
namespace php\controller;

use restphp\http\RestHttpRequest;

/**
 * Test Class
 * @author sofical
 * @date 2017-03-17
 * @package php\controller
 */
#RequestMapping(value="/v1")
final class ControllerIndex extends ControllerEmptyTest{
    #RequestMapping(value="/hello/word",method="GET")
    public function helloWASDE() {
        echo "hello world!";
    }

    #RequestMapping(value="/hello/test",method="GET")
    public function test() {
        echo "test";
    }

    #RequestMapping(value="",method="POST")
    public function create() {
        echo "create";
    }

    #RequestMapping(value="/$test_id")
    public function info() {
        echo "info";
    }

    #RequestMapping(value="/$test_id/report")
    public function inf() {
        echo "inf";
    }

    /**
     * 参数测试
     */
    #RequestMapping(value="/$test_id/report/$report_id")
    public function reportInfo() {
        var_dump(RestHttpRequest::getPathValue('test_id'));
        var_dump(RestHttpRequest::getPathValue('report_id'));
        echo "report id";
    }
}