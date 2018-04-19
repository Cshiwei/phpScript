<?php
/**
 * Created by PhpStorm.
 * User: csw
 * Date: 2018/4/19
 * Time: 14:02
 * orderMatched脚本开发环境相关配置文件
 */
$_config['orderMatched']['mailTo'] = array(
    'caoshiwei@lightinthebox.com'   =>  array('email'=>'caoshiwei@lightinthebox.com','name'=>'曹世伟'),
);
$_config['orderMatched']['mailFrom'] = 'caoshiwei@lightinthebox.com';

//csw报警阈值，异常订单超过该数量则邮件报警
$_config['orderMatched']['boundary'] = 2000;

//csw 控制订单获取的时间区间
$_config['orderMatched']['beginDiff'] = 6;
$_config['orderMatched']['endDiff'] = 1;
