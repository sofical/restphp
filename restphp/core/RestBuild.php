<?php
namespace restphp\core;

use restphp\http\RestHttpMethod;

/**
 * 构建工具
 * @author sofical
 * @date 2017-03-17
 * @package restphp\core
 */
class RestBuild{
    public static function run() {
        //先清构建目标目录
        self::_delAllFile(RestConstant::REST_TARGET() . DIRECTORY_SEPARATOR);

        //扫描构建目录
        $arrFiles = array();
        self::_loadPHPFiles($arrFiles, DIR_BUILD . DIRECTORY_SEPARATOR);
        if (empty($arrFiles)) {
            echo "build success";
            return;
        }

        //创建构建缓存
        foreach($arrFiles as $strFile) {
            $strFileContent = file_get_contents($strFile);
            $strNameSpace = self::_getNameSapce($strFileContent);
            $strClassName = self::_getClassName($strFile);

            $arrMatched = self::_getMatchs($strFileContent, '#RequestMapping', '{');

            if (!empty($arrMatched)) {
                $strClassRouteUri = "/";
                //首选判断是否为类路由
                $strClassTester = "";
                self::_getMatch($arrMatched[0], 'class', ' ', $strClassTester);
                if ($strClassTester == 'class') {
                    $strClassRouteUri = self::_getMappingUri($arrMatched[0]);
                    $strClassRouteUri == '' and $strClassRouteUri = "/";
                }

                foreach($arrMatched as $strMatched) {
                    //处理函数的路由
                    $strFuncTester = "";
                    self::_getMatch($strMatched, 'function', ' ', $strFuncTester);
                    if ($strFuncTester == 'function') {
                        $strFuncUri = self::_getMappingUri($strMatched);
                        if (!isset($strFuncUri[0]) || $strFuncUri[0] != '/') {
                            $strFuncUri = '/' . $strFuncUri;
                        }
                        if ($strFuncUri[strlen($strFuncUri)-1] != '/') {
                            $strFuncUri .= '/';
                        }

                        $strFuncName = self::_getFunctionName($strMatched);
                        $strFuncName = trim($strFuncName);
                        if ('' == $strFuncName) {
                            echo "build error in class {$strNameSpace}\\{$strClassName} of mapping text: \"{$strMatched}\"";die();
                        }

                        $strMethod = self::_getMappingMethod($strMatched);
                        if ($strMethod == "") {
                            foreach(RestHttpMethod::FULL_HTTP_METHODS() as $strMethod) {
                                self::_buildFace($strNameSpace, $strClassName, $strFuncName, $strClassRouteUri . $strFuncUri, $strMethod);
                            }
                        } else {
                            self::_buildFace($strNameSpace, $strClassName, $strFuncName, $strClassRouteUri . $strFuncUri, $strMethod);
                        }
                    }
                }
            }
        }

        //开始分创建构建结果
        self::_buildFinal();

        echo "build success!";
    }

    /**
     * 清除指定目录下所有文件
     * @param $strPath
     */
    private static function _delAllFile($strPath) {
        if (is_dir($strPath)) {
            $nodes = glob($strPath . '*');
            foreach($nodes as $node) {
                if (is_dir($node)) {
                    self::_delAllFile($node . DIRECTORY_SEPARATOR);
                    rmdir($node);
                    continue;
                }
                if ($node != '.' && $node != '..') {
                    unlink($node);
                }
            }
        }
    }

    //构建缓存变量
    private static $_arrMaps = array();

    /**
     * 构建缓存
     * @param $strNameSpace
     * @param $strClassName
     * @param $strFunctionName
     * @param $strUri
     * @param $strMethod
     */
    private static function _buildFace($strNameSpace, $strClassName, $strFunctionName, $strUri, $strMethod) {
        $strUriKey = str_replace('//', '/', $strUri);
        $arrToReplace = self::_getMatchs($strUriKey, '$', '/');
        $strMatchKey = $strUriKey;
        $arrPathParam = array();
        if (isset($arrToReplace[0])) {
            foreach($arrToReplace as $strReplace) {
                $strPathParam = substr($strReplace, 1);
                if (in_array($strPathParam, $arrPathParam)) {
                    echo "build error in uri: {$strUri} and method {$strMethod}; There are two or more same path params;";die();
                }
                $arrPathParam[] = $strPathParam;
                $strMatchKey = str_replace($strReplace, '(.*)', $strMatchKey);
            }
        }
        $strMatchKey = str_replace('/', '\/',$strMatchKey);
        if (substr($strMatchKey, strlen($strMatchKey)-6, 6) == '(.*)\/') {
            $strMatchKey = substr($strMatchKey, 0, strlen($strMatchKey)-2) . RestConstant::REST_URI_ALL_END();
        } else {
            $strMatchKey .= RestConstant::REST_URI_SIGE_END();
        }

        $strUriKey = str_replace('/', '_', $strUriKey);

        if (isset(self::$_arrMaps[$strMethod]) && isset(self::$_arrMaps[$strMethod][$strUriKey])) {
            echo "build error in uri: {$strUri} and method {$strMethod}";die();
        }

        $strFileName = str_replace('/', '_', $strUri);

        self::$_arrMaps[$strMethod][$strUriKey] = array(
            'path_param' => $arrPathParam,
            'preg_match' => $strMatchKey,
            'filename' => $strFileName,
            'namespace' => $strNameSpace,
            'class' => $strClassName,
            'function' => $strFunctionName
        );
    }

    /**
     * 构建成文件
     */
    private static function _buildFinal() {
        if(empty(self::$_arrMaps)) {
            return;
        }

        //构建Map
        foreach(self::$_arrMaps as $strMethod => $arrMaps) {
            $strMap = "<?php\nreturn array(";
            $strMethodMapFileName = RestConstant::REST_TARGET() . DIRECTORY_SEPARATOR . $strMethod . '.php';
            $intPos = 0;
            foreach($arrMaps as $strUriKey => $arrMap) {
                $strMap .= "\n\t'" . $strUriKey . "'=>array(";
                $strMap .= "\n\t\t'path_param'=>array(" ;
                if (!empty($arrMap['path_param'])) {
                    $intPosSub = 0;
                    foreach($arrMap['path_param'] as $strPathParam) {
                        $strMap .= "\n\t\t\t\t'{$strPathParam}'";
                        ++$intPosSub != count($arrMap['path_param']) and $strMap .=",";
                    }
                }
                $strMap .= "\n\t\t\t),";
                $strMap .= "\n\t\t'preg_match'=>'" . $arrMap['preg_match'] . "',";
                $strMap .= "\n\t\t'filename'=>'" . $arrMap['filename'] . "',";
                $strMap .= "\n\t\t'namespace'=>'" . $arrMap['namespace'] . "',";
                $strMap .= "\n\t\t'class'=>'" . $arrMap['class'] . "',";
                $strMap .= "\n\t\t'function'=>'" . $arrMap['function'] . "'\n\t\t)";
                ++$intPos != count($arrMaps) and $strMap .= ",";

                //构建路由入口
                $strRouteFileDir = RestConstant::REST_TARGET() . DIRECTORY_SEPARATOR . $strMethod . DIRECTORY_SEPARATOR;
                if (!file_exists($strRouteFileDir)) {
                    mkdir($strRouteFileDir);
                }
                $strRouteFileName = $strRouteFileDir . $arrMap['filename'] . '.php';
                $strFileContent = "<?php\nuse " . $arrMap['namespace'] . '\\' . $arrMap['class'] . ';';
                $strFileContent .= "\n" . '$client = new ' . $arrMap['class'] . '();';
                $strFileContent .= "\n" . '$client->' . $arrMap['function'] . '();';
                file_put_contents($strRouteFileName, $strFileContent);
            }
            $strMap .= "\n\t);";
            file_put_contents($strMethodMapFileName, $strMap);
        }
   }

    /**
     * 获取路由Uri
     * @param $strContent
     * @return string
     */
    private static function _getMappingUri($strContent) {
        $strUri = "";
        self::_getMatch($strContent, 'value="', '"', $strUri, false);
        return $strUri;
    }

    /**
     * 获取Http Method
     * @param $strContent
     * @return string
     */
    private static function _getMappingMethod($strContent) {
        $strMethod = "";
        self::_getMatch($strContent, 'method="', '"', $strMethod, false);
        return strtoupper($strMethod);
    }

    /**
     * 获取函数名
     * @param $strContent
     * @return string
     */
    private static function _getFunctionName($strContent) {
        $strFuncName = "";
        self::_getMatch($strContent, 'function ', '(', $strFuncName, false);
        return $strFuncName;
    }

    /**
     * 获取所有匹配字符串
     * @param $strContent
     * @param $strStartMatch
     * @param $strEndMatchFirst
     * @return array
     */
    private static function _getMatchs($strContent, $strStartMatch, $strEndMatchFirst) {
        $intArgNum = func_num_args();
        $bNeedStart = true;
        $intArgNum == 4 and $bNeedStart = func_get_arg(3);

        $arrMatched = array();
        $strMatched = "";
        if (self::_getMatch($strContent, $strStartMatch, $strEndMatchFirst, $strMatched, $bNeedStart)) {
            $arrMatched[] = $strMatched;
            $intStartPos = strpos($strContent, $strMatched . $strEndMatchFirst) + strlen($strMatched . $strEndMatchFirst);
            if ($intStartPos < strlen($strContent)) {
                $arrNewMatched = self::_getMatchs(substr($strContent, $intStartPos), $strStartMatch, $strEndMatchFirst, $bNeedStart);
                $arrMatched = array_merge($arrMatched, $arrNewMatched);
            }
        }
        return $arrMatched;
    }

    /**
     * 字符串匹配截取
     * @param $strContent
     * @param $strStartMatch
     * @param $strEndMatchFirst
     * @return string
     */
    private static function _getMatch($strContent, $strStartMatch, $strEndMatchFirst, &$strMatched) {
        $intArgNum = func_num_args();
        $bNeedStart = true;
        $intArgNum == 5 and $bNeedStart = func_get_arg(4);

        $bMatched = false;
        $intStartPos = strpos($strContent, $strStartMatch);
        if ($intStartPos > -1) {
            $bNeedStart or $intStartPos += strlen($strStartMatch);
            $intEndPos = strpos($strContent, $strEndMatchFirst, $intStartPos);
            if ($intEndPos > $intStartPos) {
                $bMatched = true;
                $strMatched = substr($strContent, $intStartPos, $intEndPos - $intStartPos);
            }
        }
        return $bMatched;
    }

    /**
     * 获取名称空间
     * @param $strContent
     * @return string
     */
    private static function _getNameSapce($strContent) {
        $strNameSapce = "";
        $intPos = strpos($strContent, "namespace");
        if ($intPos > -1) {
            $intStartPos = $intPos + strlen("namespace");
            $strNameSapce = substr($strContent, $intStartPos, strpos($strContent, ";", $intStartPos) - $intStartPos);
        }
        return trim($strNameSapce);
    }

    /**
     * 获取类名
     * @param $strFileName
     * @return string
     */
    private static function _getClassName($strFileName) {
        $intStartPos = strrpos($strFileName, DIRECTORY_SEPARATOR) + 1;
        $strClassName = substr($strFileName, $intStartPos, strlen($strFileName) - $intStartPos - 4);
        return $strClassName;
    }

    /**
     * 扫描获取所有项目
     * @param $arrFiles
     * @param $strDir
     */
    private static function _loadPHPFiles(&$arrFiles, $strDir) {
        if (is_dir($strDir)) {
            $arr = glob($strDir . "*php");
            $arrFiles = array_merge($arrFiles, $arr);
            $arrDirs = glob($strDir . '*' . DIRECTORY_SEPARATOR);
            if (!empty($arrDirs)) {
                foreach ($arrDirs as $strSubDir) {
                    self::_loadPHPFiles($arrFiles, $strSubDir);
                }
            }
        }
    }
}