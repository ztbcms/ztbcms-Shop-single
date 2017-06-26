<?php
/**
 * 提成交易记录servicce
 */
namespace Record\Service;

use Record\Libs\Record;
use Record\Model\RecordModel;
use Shop\Records\CommissionRecord;
use Shop\Service\OrderService;

class CommissionRecordService extends RecordService {
    const TABLE_NAME = 'RecordCommission';

    /**
     * 创建一个交易记录
     *
     * @param        $to          流入者id
     * @param        $target_type 记录产生类型
     * @param        $target      记录产生唯一标识
     * @param int    $from        来源id
     * @param int    $from_type   来源类型
     * @param int    $income      收入
     * @param int    $pay         支出
     * @param int    $status      记录状态
     * @param string $detail
     * @return array
     */
    static function createCommissionRecord(
        $to,
        $target_type,
        $target,
        $from = 0,
        $from_type,
        $income = 0,
        $pay = 0,
        $status = RecordModel::STATUS_VAILD,
        $detail = ''
    ) {
        $commission_recored = new CommissionRecord($to, $target_type, $target);
        $commission_recored->setIncome($income);
        $commission_recored->setPay($pay);
        $commission_recored->setStatus($status);
        $commission_recored->setDetail($detail);

        $commission_recored->setFrom($from);
        $commission_recored->setFromType($from_type);

        return self::createRrcord($commission_recored);
    }

    /**
     *通过 to 获取所属交易列表
     *
     * @param        $to
     * @param string $to_type
     * @param int    $status
     * @param int    $page
     * @param int    $limit
     * @param string $order
     * @return array
     */
    static function getCommissionRecordList(
        $to,
        $to_type = 'member',
        $status = RecordModel::STATUS_VAILD,
        $page = 1,
        $limit = 20,
        $order = ''
    ) {
        $where = [
            'to' => $to,
            'to_type' => $to_type,
            'status' => $status
        ];

        $lists = self::selectBy(self::TABLE_NAME, $where, $order, $page, $limit)['data'];
        $total = M(self::TABLE_NAME)->where($where)->count();
        $data = [
            'lists' => $lists,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'page_count' => ceil($total / $limit)
        ];

        return self::createReturn(true, $data, 'ok');
    }

    /**
     * @param $from
     * @param $from_type
     * @return array
     */
    static function updateFrozenCommission($from, $from_type) {
        $where['from'] = $from;
        $where['from_type'] = $from_type;
        $update = [
            'status' => RecordModel::STATUS_VAILD,
            'update_time' => time()
        ];
        $res = M(self::TABLE_NAME)->where($where)->save($update);
        if ($res) {
            return self::createReturn(true, $res, 'ok');
        } else {
            return self::createReturn(false, '', '');
        }
    }

    static function getBalanceFrozen(Record $record) {
        $where = [
            'to' => $record->getTo(),
            'to_type' => $record->getToType(),
            'status' => RecordModel::STATUS_FROZEN
        ];
        $lists = M($record->table_name)->field('income,pay')->where($where)->select();
        $total = 0;
        for ($i = 0; $i < count($lists); $i++) {
            $total = $total + $lists[$i]['income'] - +$lists[$i]['pay'];
        }

        return self::createReturn(true, $total, 'ok');
    }

    static function getCommissionOrderList($where, $page = 1, $limit = 20, $order = '') {
        $commission_lists = self::selectBy(self::TABLE_NAME, $where, $order, $page, $limit)['data'];
        $lists = [];
        foreach ($commission_lists as $key => $value) {
            //获取各个返利产生的订单详情
            $order = M(OrderService::TABLE_NAME)->where(['order_sn' => $value['target']])->find();
            $order['goods_list'] = OrderService::get_order_goods($order['order_id']);
            $order['user'] = M('ShopUsers')->field('name,nickname,level')->find($order['user_id']);
            $order['commission'] = $value;
            $lists[] = $order;
        }
        $total = M(self::TABLE_NAME)->where($where)->count();
        $data = [
            'lists' => $lists,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'page_count' => ceil($total / $limit),
            'order_status' => OrderService::ORDER_STATUS(),
            'pay_status' => OrderService::PAY_STATUS(),
            'shipping_status' => OrderService::SHIPPING_STATUS()
        ];

        return self::createReturn(true, $data, '');
    }
}