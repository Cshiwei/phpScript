<?php
/**
 * Created by PhpStorm.
 * User: csw
 * Date: 2018/4/18
 * Time: 18:22
 */

class demo extends controller{

    public $orderDb;

    public $config;

    public function __construct()
    {
        parent::__construct();
        //建立数据库连接
        $this->orderDb = $this->loadDb('db_order');
        //获取本次任务需要的配置项
        $this->configItem('mailer');
    }

    public function run()
    {
        $serialNumber = '0000000643';
        $query = "SELECT * FROM `v3_orders` LIMIT 1";
        $result = $this->orderDb->fetch($query,'');

        $argv = $this->_argv;
        $scriptName = $this->_scriptName;
    }
}
