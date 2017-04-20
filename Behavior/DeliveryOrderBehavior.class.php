<?php

// +----------------------------------------------------------------------
// | 用户确认收货时调用
// +----------------------------------------------------------------------

namespace Shop\Behavior;

use Record\Model\RecordModel;
use Record\Service\CommissionRecordService;
use Record\Service\IntegralRecordService;
use Think\Log;

class DeliveryOrderBehavior {

    public function run(&$order) {
        Log::write($order['order_sn'] . '确认收货');
        $integral_res = $this->getIntegral($order);
        $commission_res = $this->updateFrozenCommission($order);
        Log::write('积分获取情况：' . json_encode($integral_res));
        Log::write('提成获取情况：' . json_encode($commission_res));
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
            $order['order_amount'], 0, RecordModel::STATUS_VAILD, '购买商品');
    }

    /**
     * 更新冻结的提成
     *
     * @param $order
     * @return array
     */
    protected function updateFrozenCommission($order) {
        //将相关的提成解冻
        return CommissionRecordService::updateFrozenCommission($order['order_sn'], 'commission');
    }
}
