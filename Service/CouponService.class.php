<?php
/**
 * Created by PhpStorm.
 * User: ningshenglee
 */

namespace Shop\Service;


class CouponService extends BaseService
{
    /**
     * 优惠券状态
     */
    const COUPON_STATUS_WUXIAO = 0;//无效
    const COUPON_STATUS_NOTUSE = 1;//未使用
    const COUPON_STATUS_ISUSE = 2;//已使用
    const COUPON_STATUS_PASSTIME = 3;//过期

    /**
     * 获取用户优惠券列表
     * @param array $where 查询条件
     * @param array $total_money 支付总金额
     * @return mixed
     */
    static function getCounponList($where,$total_money)
    {
        if($total_money){
            //判断优惠券是否满足使用条件：支付总金额超过满减价格并且优惠价格不能超过支付总金额
            $where['full_price'] = array('lt',$total_money);
            $where['discount_price'] = array('lt',$total_money);
        }
        $res = M('ShopUsercoupon')->where($where)->select();
        return $res;
    }

    /**
     * 获取用户优惠券详情
     * @param int $id 用户优惠券ID
     * @param int $userid 所属用户ID
     * @return mixed
     */
    static function getUserCouponInfo($id,$userid)
    {
        $where = array(
            'id' => $id,
            'userid' => $userid
        );
        $res = M('ShopUsercoupon')->where($where)->find();//用户优惠券详情
        return $res;
    }


    /**
     * @param $id
     * @param $userid
     * @param $order_id
     * @param $order_type
     * @param int $status
     * @return bool
     */
    static function useCoupon($id, $userid, $order_id, $order_type, $status = self::COUPON_STATUS_ISUSE)
    {
        $where = array(
            'id' => $id,
            'userid' => $userid
        );
        $order = M('Order')->where("order_id = $order_id")->find();
        $data = array(
            'order_sn' => $order['order_sn'],
            'order_type' => $order_type,
            'status' => $status
        );
        $res = M('ShopUsercoupon')->where($where)->save($data);
        return $res;
    }
}