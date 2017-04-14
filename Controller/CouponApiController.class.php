<?php
/**
 * Created by PhpStorm.
 * User: Ningshenglee
 * 商城优惠券和用户优惠券
 */

namespace Shop\Controller;


use Shop\Service\CouponService;
use Shop\Service\OrderService;

class CouponApiController extends BaseController
{
    /**
     * 初始化操作
     */
    public function _initialize() {
        parent::_initialize();
    }
    /**
     * 获取用户优惠券列表
     */
    public function coupon_lists()
    {
        $where = [];
        $userid = $this->userid;
        $where['userid'] = $userid;
        if (I('get.status')) {
            $where['status'] = I('get.status');
        }
        if(I('get.cart_ids')){
            $where_cart['userid'] = $userid;
            $where_cart['id'] = array('in', I('get.cart_ids'));
            $order_goods = M('Cart')->where($where_cart)->select();
            //检测购物车是否有选择商品
            if (count($order_goods) == 0) {
                $this->error('你的购物车没有选中商品');
            } // 返回结果状态
            $order_service = new OrderService();
            //按选中购物车的商品，计算出各个部分的价格
            $result = $order_service->calculate_price($this->userid, $order_goods);
            if (!$result) {
                $this->error($order_service->get_err_msg());
            }
            $total_money = $result['order_amount'];//支付总金额
        }else{
            $total_money = '';
        }

        $coupon_list = CouponService::getCounponList($where,$total_money);
        $this->success($coupon_list,'',true);
    }

    /**
     * 获取用户优惠券详情
     */
    public function coupon_info()
    {
        $id = I('id');//用户优惠券
        $user_coupon = CouponService::getUserCouponInfo($id,$this->userid);
        $this->success($user_coupon,'',true);
    }

    /**
     * 减去优惠价后的应付价格
     */
    public function cut_discount_price()
    {
        if(I('id')){
            $id = I('id');//用户优惠券
            $user_coupon = M('ShopUsercoupon')->where("id = $id")->find();//用户优惠券详情
            $discount_price = $user_coupon['discount_price'];
        }else{
            $discount_price = 0;
        }

        $where_cart['userid'] = $this->userid;
        $where_cart['id'] = array('in', I('cart_ids'));
        $order_goods = M('Cart')->where($where_cart)->select();
        //检测购物车是否有选择商品
        if (count($order_goods) == 0) {
            $this->error('你的购物车没有选中商品');
        } // 返回结果状态
        $order_service = new OrderService();
        //按选中购物车的商品，计算出各个部分的价格
        $result = $order_service->calculate_price($this->userid, $order_goods);
        if (!$result) {
            $this->error($order_service->get_err_msg());
        }
        $total_money = $result['order_amount'];//支付总金额
        $result_total_money = $total_money - $discount_price;
        $this->success($result_total_money,'',true);
    }

    /**
     * 使用优惠券
     */
    public function use_coupon()
    {
        $id = I('usercoupon_id');//优惠券ID
        $order_id = I('order_id');//订单ID
        $order_type = I('order_type','order');//订单类型
        $result = CouponService::useCoupon($id,$this->userid,$order_id,$order_type);
        $this->success($result,"",true);
    }
}