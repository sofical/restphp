<?php
namespace restphp;

use restphp\core\RestRun;
use restphp\core\RestBuild;
use restphp\core\RestConstant;
use restphp\utils\RestFileUtil;

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
    public static function run($bAutoBuild = false, $strVersion = "default") {
        //注入类文件加载器
        self::_before();

        $strVersionPath = RestConstant::REST_TARGET() . DIRECTORY_SEPARATOR . $strVersion . DIRECTORY_SEPARATOR;
        if ($bAutoBuild && !file_exists($strVersionPath)) {
            RestBuild::run($strVersion);
        }

        //rest路由开始
        RestRun::run($strVersion);
    }

    /**
     * 打包项目
     */
    public static function build($strVersion = "default") {
        //注入类文件加载器
        self::_before();

        //执行打包构建
        RestBuild::run($strVersion);
    }
}