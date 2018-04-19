<?php
/**
 * Created by PhpStorm.
 * User: csw
 * Date: 2018/4/18
 * Time: 15:24
 * 把最近一段时间已经配货完毕，但是一直还未发送库房的订单统计出来。如果订单数量大于一定数量，则发邮件报警。
 */
require_once '../init.php';
ini_set('memory_limit', '128M');

//加载配置文件
$_C->load('db');
$_C->load('orderMatched');

//csw 控制订单的获取时间区间
$beginDiff = $_C->orderMatched['beginDiff'];
$endDiff = $_C->orderMatched['endDiff'];

$beginTime = date('Y-m-d H:i:s',strtotime("- {$beginDiff} day",time()));
$endTime = date('Y-m-d H:i:s',strtotime("- {$endDiff} day",time()));

//csw 报警阈值
$boundary = $_C->orderMatched['boundary'];

//初始化数据库连接
$dbOrderConfig = $_C->item('db_order');
$_D = new orgDB($dbOrderConfig['dsn'],$dbOrderConfig['username'],$dbOrderConfig['password']);

//如果异常订单数量超过阈值则发送邮件提醒
$sql = "SELECT o.orders_id as o_orders_id,i.states as i_states FROM v3_orders o LEFT JOIN v3_orders_items i ON o.orders_id=i.orders_id 
        WHERE o.states='AllMatched' 
        AND o.last_updated>='{$beginTime}' 
        AND o.last_updated<='{$endTime}'";

$res = $_D->fetch($sql,'');
$errorOrders = array();
if($res)
{
    foreach ($res as $key=>$val)
    {
        $errorOrders[$val['o_orders_id']] = 1;
        if($val['i_states']=='Transferring')

    }
}

$resCount = $_D->fetch($sql,'');
if($resCount > $boundary)
{
    $mail = new PHPMailer();
    $body = '<h1>已经配送完毕但是一直还未送出的订单统计</h1>';
}




