<?php

// +----------------------------------------------------------------------
// | 用户确认收货时调用
// +----------------------------------------------------------------------

namespace Shop\Behavior;

use Record\Service\IntegralRecordService;
use Think\Log;

class DeliveryOrderBehavior {
    public function run(&$order) {
        Log::write($order['order_sn'] . '确认收货');
        $this->getIntegral($order);
    }

    /**
     * 获取积分操作
     *
     * @param $order
     * @return array
     */
    protected function getIntegral($order) {
        //确认收货之后，获取积分。订单总价多少就获得多少积分。
        //TODO 根据业务需要可以更换代码
        return IntegralRecordService::createIntegralRecord($order['user_id'], 'buy_goods', $order['order_sn'],
            $order['order_amount']);
    }
}
