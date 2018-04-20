<?php
/**
 * Created by PhpStorm.
 * User: csw
 * Date: 2018/4/20
 * Time: 13:03
 * 把最近一段时间已经配货完毕，但是一直还未发送库房的订单统计出来。如果订单数量大于一定数量，则发邮件报警。
 */
class orderMatch extends controller{

    public $orderObj;

    public $orderConfig;

    public $logPath ='ordermatch';

    public function __construct()
    {
        parent::__construct();
        $this->orderConfig = $this->configItem('orderMatched');
        $this->orderObj = $this->loadDb('db_order');
    }

    public function run()
    {
        //csw 控制订单的获取时间区间
        $beginDiff = $this->orderConfig['beginDiff'];
        $endDiff = $this->orderConfig['endDiff'];

        $beginTime = date('Y-m-d H:i:s',strtotime("- {$beginDiff} day",time()));
        $endTime = date('Y-m-d H:i:s',strtotime("- {$endDiff} day",time()));
        //csw 报警阈值
        $boundary = $this->orderConfig['boundary'];

        $res = $this->getNewErrorOrder($beginTime,$endTime);
        if(!$res)
            $msg = '未获取到问题订单';
        else
        {
            $errCount = count($res);
            $msg = "{$beginTime}-{$endTime}问题单数量:{$errCount}";
        }
        logMsg($msg,$this->logPath);
    }

    /**csw
     * 获取问题订单
     */
    public function getNewErrorOrder($beginTime,$endTime)
    {
        $orderMatched = $this->getOrdersMatched($beginTime,$endTime);
        if($orderMatched)
        {
            $temp = array();
            foreach ($orderMatched as $key=>$val)
            {
                $temp[$val['o_orders_id']][] = $val;
            }

            foreach ($orderMatched as $key=>$val)
            {
                if($val['i_states']=='Transferring')
                    unset($temp[$val['o_orders_id']]);
            }
        }
        return $temp;
    }

    /**csw
     * @param $beginTime
     * @param $endTime
     * 获取更新时间在一定时间区间内的已经配货完毕的订单信息
     */
    public function getOrdersMatched($beginTime,$endTime)
    {
        $sql = "SELECT o.orders_id as o_orders_id,i.states as i_states,i.orders_items_id as i_orders_items_id FROM v3_orders o LEFT JOIN v3_orders_items i ON o.orders_id=i.orders_id 
        WHERE o.states='AllMatched' 
        AND o.last_updated>='{$beginTime}' 
        AND o.last_updated<='{$endTime}'";

        $result = $this->orderObj->fetch($sql);
        return $result;
    }


    /**csw
     *
     */
    public function getOrdersTrans($beginTime,$endTime)
    {
        $sql = "SELECT o.orders_id as o_orders_id FROM v3_orders o LEFT JOIN v3_orders_items i ON o.orders_id=i.orders_id 
        WHERE o.states='AllMatched' 
        AND i.states = 'Transferring' 
        AND o.last_updated>='{$beginTime}' 
        AND o.last_updated<='{$endTime}'";

        $resOrders = $this->orderObj->fetch($sql);
        if(!$resOrders)
            return false;

        return array_unique(array_column($resOrders,'o_orders_id'));
    }

    /**csw
     * 获取异常的订单
     */
    public function getErrorOrder($beginTime,$endTime,$transOrders)
    {
        $errorOrderStr = implode(',',$transOrders);
        $sql = "SELECT o.orders_id as o_orders_id FROM v3_orders o LEFT JOIN v3_orders_items i ON o.orders_id=i.orders_id 
        WHERE o.states='AllMatched' 
        AND o.orders_id NOT IN ({$errorOrderStr})  
        AND o.last_updated>='{$beginTime}' 
        AND o.last_updated<='{$endTime}'";

        $resErrorOrders = $this->orderObj->fetch($sql);
        if(!$resErrorOrders)
            return false;

        return $resErrorOrders;
    }
}
