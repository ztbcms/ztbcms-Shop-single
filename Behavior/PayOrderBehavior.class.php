<?php

// +----------------------------------------------------------------------
// | 用户支付成功时调用
// +----------------------------------------------------------------------

namespace Shop\Behavior;

use Record\Model\RecordModel;
use Record\Service\CommissionRecordService;
use Think\Log;

class PayOrderBehavior {

    public function run(&$order) {
        Log::write($order['order_sn'] . '确认收货');
        $commission_res = $this->getCommission($order);
        Log::write('提成获取情况：' . json_encode($commission_res));
    }

    /**
     * 计算订单的提成
     *
     * @param $order
     * @return array
     */
    protected function getCommission($order) {
        //获取订单的商城用户信息
        $userid = $order['user_id'];
        $user = M('ShopUsers')->where(['userid' => $userid])->find();
        $order_sn = $order['order_sn'];
        $order_amount = $order['order_amount'];
        //获取配置信息
        $config = cache('Config');
        if ($user['first_leader']) {
            //用户有一级代理，计算一级代理的提成
            $first_commission = sprintf('%0.2f', $order_amount * $config['shop_first_commission']);
            //为总代添加交易信息,状态为冻结
            $res = CommissionRecordService::createCommissionRecord($user['first_leader'], 'first_commission', $order_sn,
                $order_sn, 'commission', $first_commission, 0, RecordModel::STATUS_FROZEN, '总代提成');
            if (!$res['status']) {
                return $res;
            }
        }

        if ($user['second_leader']) {
            //用户有二级代理，计算二级代理的提成
            $second_commission = sprintf('%0.2f', $order_amount * $config['shop_second_commission']);
            $res = CommissionRecordService::createCommissionRecord($user['second_leader'], 'second_commission',
                $order_sn, $order_sn, 'commission', $second_commission, 0, RecordModel::STATUS_FROZEN, '一级代理提成');
            if (!$res['status']) {
                return $res;
            }
        }

        if ($user['third_leader']) {
            //用户有三级代理，计算三级代理的提成
            $third_commission = sprintf('%0.2f', $order_amount * $config['shop_third_commission']);

            $third_leader = M('ShopUsers')->where(['userid' => $user['third_leader']])->find();
            if ($third_leader['direct_leader']) {
                //找到三级用户的直接上级
                $third_leader_direct = M('ShopUsers')->where(['userid' => $third_leader['direct_leader']])->find();
                if ($third_leader_direct['level'] == 3) {
                    //该上级用户的直接上级也是三级用户，则分得50%
                    CommissionRecordService::createCommissionRecord($user['third_leader'], 'third_commission_half',
                        $order_sn, $order_sn, 'commission', sprintf('%0.2f', $third_commission), 0,
                        RecordModel::STATUS_FROZEN, '经销商统计提成');
                    $res = CommissionRecordService::createCommissionRecord($third_leader_direct['userid'],
                        'third_commission_half', $order_sn, $order_sn, 'commission',
                        sprintf('%0.2f', $third_commission), 0, RecordModel::STATUS_FROZEN, '经销商统计提成');
                } else {
                    $res = CommissionRecordService::createCommissionRecord($user['third_leader'], 'third_commission',
                        $order_sn, $order_sn, 'commission', $third_commission, 0, RecordModel::STATUS_FROZEN, '经销商提成');
                }
            } else {
                $res = CommissionRecordService::createCommissionRecord($user['third_leader'], 'third_commission',
                    $order_sn, $order_sn, 'commission', $third_commission, 0, RecordModel::STATUS_FROZEN, '经销商提成');
            }
            if (!$res['status']) {
                return $res;
            }
        }

        return $res;
    }
}
