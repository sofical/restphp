<?php
/**
 * 构建入口
 * @author sofical
 * @date 2017-03-17
 */

//引入目录配置
require('config/rest.config.php');

//引入框架
require(DIR_RESTPHP . '/Rest.php');

\restphp\Rest::build();