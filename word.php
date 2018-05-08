<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/18
 * Time: 14:38
 */
$file = "C:\Users\Administrator\Downloads\base (2).txt";
$content = file_get_contents($file);
$strWords =str_replace("\n", "\",\"", $content);
echo $strWords;