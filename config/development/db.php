<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/18
 * Time: 19:25
 */

/****************   订单表   ****************************************************/
//csw 标记数据库连接池唯一的一个连接
$_config['db_order']['connection'] = 'db_order';
$_config['db_order']['server'] = '172.16.0.96';
$_config['db_order']['username'] = 'order_bj';
$_config['db_order']['password'] = 'dpvYprwh69MN';
$_config['db_order']['database'] = 'products_center_v1';
$_config['db_order']['port']  = 3306;
$_config['db_order']['charset'] = 'utf8';
$_config['db_order']['dsn'] = "mysql:host={$_config['db_order']['server']};port={$_config['db_order']['port']};dbname={$_config['db_order']['database']};charset={$_config['db_order']['charset']}";
/****************  订单表   *******************************************************/


/******************  org系统表  ***************************************************/
$_config['db_org']['connection'] = 'db_org';
$_config['db_org']['server'] = '192.168.3.154';
$_config['db_org']['username'] = 'sa';
$_config['db_org']['password'] = 'litb2015';
$_config['db_org']['database'] = 'testHRMLITB';
$_config['db_org']['port']  = 3306;
$_config['db_org']['charset'] = 'utf8';
$_config['db_org']['dsn'] = "sqlsrv:host={$_config['db_org']['server']};port={$_config['db_org']['port']};dbname={$_config['db_org']['database']};charset={$_config['db_org']['charset']}";
/************************  org系统表  ************************************************/
