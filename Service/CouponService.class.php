<?php
/**
 * Created by PhpStorm.
 * User: ningshenglee
 */

namespace Shop\Service;


class CouponService extends BaseService
{
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
}