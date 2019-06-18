<?php
//开发配置定义

//当前环镜
define('PROJ_ENV', 'dev');

//环镜参数
$_EVN_PARAM_ALL = include('env.config.php');
$_EVN_PARAM = $_EVN_PARAM_ALL[PROJ_ENV];
$_DB_MYSQL = isset($_EVN_PARAM) ? $_EVN_PARAM['DATABASE_MYSQL'] : array();

//加载多语言
$_LANG = include('lang.config.php');
