<?php
/**
 * Created by zj.
 * User: zj
 * Date: 2019/8/21 0021
 * Time: 上午 10:35
 */
namespace restphp\utils;

final class RestLog {
    /**
     * 记录日志.
     * @param $strContent
     * @param $strPre
     * @param int $intLogAppendType
     */
    public static function writeLog($strContent, $strPre = 'runtime.', $intLogAppendType = 1) {
        if (!defined('APP_LOG_SWITCH') || 1!=APP_LOG_SWITCH) {
            return;
        }
        if (!defined('APP_LOG_DIR')) {
            return;
        }
        if (!is_dir(APP_LOG_DIR)) {
            return;
        }

        $strLogFile = APP_LOG_DIR .DIRECTORY_SEPARATOR . $strPre . date('Y-m-d') . '.log';

        $contentPre = "time: " . date('Y-m-d H:i:s') . "\n";
        @file_put_contents($strLogFile, "\n\n" . $contentPre . $strContent, FILE_APPEND);
    }
}