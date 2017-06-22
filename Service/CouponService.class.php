<?php
/**
 * Created by PhpStorm.
 * User: ningshenglee
 */

namespace Shop\Service;


class CouponService extends BaseService {
    /**
     * 优惠券状态
     */
    const COUPON_STATUS_WUXIAO = 0;//无效
    const COUPON_STATUS_NOTUSE = 1;//未使用
    const COUPON_STATUS_ISUSE = 2;//已使用
    const COUPON_STATUS_PASSTIME = 3;//过期

    /**
     * 获取用户优惠券列表
     *
     * @param array $where       查询条件
     * @param string $total_money 支付总金额
     * @return mixed
     */
    static function getCounponList($where, $total_money) {
        if ($total_money > 0) {
            //判断优惠券是否满足使用条件：支付总金额超过满减价格并且优惠价格不能超过支付总金额
            $where['full_price'] = array('lt', $total_money);
        }
        $res = M('ShopUsercoupon')->where($where)->select();

        return self::createReturn(true, $res ? $res : [], 'ok');
    }

    /**
     * 获取用户优惠券详情
     *
     * @param int $id     用户优惠券ID
     * @param int $userid 所属用户ID
     * @param int $status
     * @return array
     */
    static function getUserCouponInfo($id, $userid, $status = self::COUPON_STATUS_NOTUSE) {
        $where = array(
            'id' => $id,
            'userid' => $userid,
            'status' => $status
        );
        $res = M('ShopUsercoupon')->where($where)->find();//用户优惠券详情
        if ($res) {
            return self::createReturn(true, $res, 'ok');
        } else {
            return self::createReturn(false, $res, '该优惠券不存在');
        }
    }


    /**
     * 使用优惠券
     *
     * @param int $id         优惠券ID
     * @param int $userid     用户ID
     * @param int $order_id   订单ID
     * @param int $order_type 订单类型
     * @param int $status     优惠券状态
     * @return array
     */
    static function useCoupon($id, $userid, $order_id, $order_type, $status = self::COUPON_STATUS_ISUSE) {
        $where = array(
            'id' => $id,
            'userid' => $userid
        );
        $order = M('Order')->where("order_id = $order_id")->find();
        $data = array(
            'order_sn' => $order['order_sn'],
            'order_type' => $order_type,
            'status' => $status,
            'use_time' => time()//使用时间
        );
        $res = M('ShopUsercoupon')->where($where)->save($data);

        if ($res) {
            return self::createReturn(true, $id, 'ok');
        } else {
            return self::createReturn(false, '', '没有数据修改');
        }
    }

    /**
     * 获取扣减优惠后的商品价格
     * @param $id
     * @param $cart_ids
     * @param $userid
     * @return array|bool
     */
    static function cutDiscountPrice($id, $cart_ids, $userid) {
        if ($id) {
            $where = [
                'id' => $id,
                'userid' => $userid
            ];
            $user_coupon = M('ShopUsercoupon')->where($where)->find();//用户优惠券详情
            $discount_price = $user_coupon['discount_price'];
        } else {
            $discount_price = 0;
        }

        $where_cart['userid'] = $userid;
        $where_cart['id'] = array('in', $cart_ids);
        $order_goods = M(CartService::TABLE_NAME)->where($where_cart)->select();
        //检测购物车是否有选择商品
        if (count($order_goods) == 0) {
            return self::createReturn(true, '', '你的购物车没有选中商品');
        } // 返回结果状态
        //按选中购物车的商品，计算出各个部分的价格
        $result = OrderService::calculatePrice($userid, $order_goods);
        if (!$result['status']) {
            return $result;
        }
        $total_money = $result['data']['order_amount'];//支付总金额
        if (isset($user_coupon['full_price']) && $user_coupon['full_price'] < $total_money) {
            //该订单价格满足满减
            $result_total_money = $total_money - $discount_price;
        } else {
            $result_total_money = $total_money;
        }

        return self::createReturn(true, $result_total_money > 0 ? $result_total_money : 0, '');
    }

    /**
     * 生成随机的优惠券编码
     * @param int $length 随机数长度
     * @return int
     */
    static function generate_code($length)
    {
        return rand(pow(10,($length-1)), pow(10,$length)-1);
    }
}