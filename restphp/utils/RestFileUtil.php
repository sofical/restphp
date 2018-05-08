<?php
/**
 * Created by PhpStorm.
 * User: zj
 * Date: 2017/8/2
 * Time: 16:41
 */
namespace restphp\utils;

final class RestFileUtil {
    /**
     * 清除指定目录下所有文件
     * @param $strPath
     */
    public static function delAllFile($strPath) {
        if (is_dir($strPath)) {
            $nodes = glob($strPath . '*');
            foreach($nodes as $node) {
                if (is_dir($node)) {
                    self::delAllFile($node . DIRECTORY_SEPARATOR);
                    rmdir($node);
                    continue;
                }
                if ($node != '.' && $node != '..') {
                    unlink($node);
                }
            }
        }
    }
}