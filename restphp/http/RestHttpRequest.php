<?php
namespace restphp\http;

use restphp\biz\RestContentType;
use restphp\utils\RestXmlUtil;

/**
 * Class RestHttpRequest
 * @author sofical
 * @date 2017-03-17
 * @package restphp\http
 */
final class RestHttpRequest{
    private static $_arrServer = array();
    private static $_arrRequest = array();
    private static $_arrPost = array();
    private static $_arrGet = array();
    private static $_arrPath = array();
    private static $_oBody = null;

    /**
     * Request init
     */
    public static function init() {
        self::$_arrServer = empty($_SERVER) ? self::$_arrServer : $_SERVER;
        self::$_arrRequest = empty($_REQUEST) ? self::$_arrRequest : $_REQUEST;
        unset($_REQUEST);
        self::$_arrPost = empty($_POST) ? self::$_arrPost : $_POST;
        unset($_POST);
        self::$_arrGet = empty($_GET) ? self::$_arrGet : $_GET;
        unset($_GET);
        self::$_arrPath = array();

        $strBody = file_get_contents('php://input');
        if ($strBody != null) {
            $strContentType = self::getServer('CONTENT_TYPE');
            switch($strContentType) {
                case RestContentType::JSON:
                    self::$_oBody = json_decode($strBody, true);
                    break;
                case RestContentType::XML;
                    self::$_oBody = RestXmlUtil::xmlToArr($strBody);
                    break;
                case '':
                default:
                    self::$_oBody = $strBody;
                    break;
            }
        }
    }

    /**
     * 设置路径变量
     * @param $strKey String 变量名
     * @param $strVal String 变量值
     */
    public static function setPathValue($strKey, $strVal) {
        self::$_arrPath[$strKey] = $strVal;
    }

    /**
     * 获取路径变量值
     * @param $strKey String 变量名称
     * @return null | String
     */
    public static function getPathValue($strKey) {
        return isset(self::$_arrPath[$strKey]) ? self::$_arrPath[$strKey] : null;
    }

    /**
     * 获取$_SERVER指定key对应的值
     * @param $strName String SERVER变量名
     * @return null
     */
    public static function getServer($strName) {
        return isset($_SERVER[$strName]) ? $_SERVER[$strName] : null;
    }

    /**
     * 获取请求内容
     * @return null | String
     */
    public static function getRequestBody() {
        return self::$_oBody;
    }

    /**
     * 获取$_REQUEST指定key对应的值
     * @param $strName String REQUEST变量名
     * @return null | Object
     */
    public static function getRequest($strName) {
        return isset(self::$_arrRequest[$strName]) ? self::$_arrRequest[$strName] : null;
    }

    /**
     * 获取$_POST指定key对应的值
     * @param $strName String POST变量名
     * @return null | Object
     */
    public static function getPost($strName) {
        return isset(self::$_arrPost[$strName]) ? self::$_arrPost[$strName] : null;
    }

    /**
     * 获取$_GET指定key对应的值
     * @param $strName String GET变量名
     * @return null
     */
    public static function getGet($strName) {
        return isset(self::$_arrGet[$strName]) ? self::$_arrGet[$strName] : null;
    }
}