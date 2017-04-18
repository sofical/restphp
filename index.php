<?php
/**
 * 程序入口
 * @author sofical
 * @date 2017-03-17
 */

//引入RESTPHP配置
require('config/rest.config.php');

//引入项目配置
require('config/proj.config.php');

//引入框架
require(DIR_RESTPHP . '/Rest.php');

\restphp\Rest::run();