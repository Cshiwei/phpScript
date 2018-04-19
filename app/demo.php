<?php
/**
 * Created by PhpStorm.
 * User: csw
 * Date: 2018/4/18
 * Time: 18:22
 * 例子脚本
 * $_C用于加载配置项
 */
require_once '../init.php';
set_time_limit(500);
ini_set('memory_limit', '128M');

//加载配置文件
$_C->load('mailer');
