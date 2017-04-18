<?php
namespace restphp;

use restphp\core\RestRun;
use restphp\core\RestBuild;

include('core/RestLoad.php');

/**
 * Class Rest
 * @author sofical
 * @date 2017-03-17
 * @package restphp
 */
class Rest{

    public static function _before() {
        spl_autoload_register(array('restphp\core\RestLoad','load'));
    }

    /**
     * 执行入口
     */
    public static function run() {
        //注入类文件加载器
        self::_before();

        //rest路由开始
        RestRun::run();
    }

    /**
     * 打包项目
     */
    public static function build() {
        //注入类文件加载器
        self::_before();

        //执行打包构建
        RestBuild::run();
    }
}