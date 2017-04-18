<?php
namespace php\controller;

#RequestMapping(value="")
class ControllerEmptyTest{
    #RequestMapping(value="", method="GET")
    public function index() {
        echo 'welcome to use restphp';
    }
}