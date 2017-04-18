<?php
namespace Shop\Controller;

use Shop\Service\CouponService;
use Shop\Service\OrderService;

class OrderApiController extends BaseController {
    /**
     * 初始化操作
     */
    public function _initialize() {
        parent::_initialize();
    }


    /**
     * 订单详情
     */
    public function order_detail() {
        $id = I('get.id');
        $map['order_id'] = $id;
        $map['user_id'] = $this->userid;
        $order_info = M('order')->where($map)->find();

        $order_info['province_name'] = getRegionName($order_info['province'], 1);
        $order_info['city_name'] = getRegionName($order_info['city'], 2);
        $order_info['district_name'] = getRegionName($order_info['district'], 3);

        if (!$order_info) {
            $this->error('查不到指定订单信息');
        }
        //获取订单商品
        $order_service = new OrderService();
        $data = $order_service->get_order_goods($order_info['order_id']);
        $order_info['goods_list'] = $data;

        //获取订单操作记录
        $order_action = M('order_action')->where(array('order_id' => $id))->select();

        //订单状态对应的中文描述
        $res_data['order_status'] = OrderService::ORDER_STATUS();
        //订单物流状态对应的中文描述
        $res_data['shipping_status'] = OrderService::SHIPPING_STATUS();
        //订单支付状态
        $res_data['pay_status'] = OrderService::PAY_STATUS();
        $res_data['order_info'] = $order_info;
        $res_data['order_action'] = $order_action;
        $this->success($res_data, '', true);
    }

    /*
    * 订单列表
    */
    public function order_list($page = 1, $limit = 10) {
        $where['user_id'] = $this->userid;
        if (I('get.order_status') != '') {
            $where['order_status'] = I('get.order_status');
        }
        if (I('get.pay_status') != '') {
            $where['pay_status'] = I('get.pay_status');
        }
        if (I('get.shipping_status') != '') {
            $where['shipping_status'] = I('get.shipping_status');
        }
        $total = M('order')->where($where)->count();
        $order_str = "order_id DESC";
        $order_list = M('order')->order($order_str)->where($where)->page($page, $limit)->select();

        //获取订单商品
        foreach ($order_list as $k => $v) {
            $data = OrderService::get_order_goods($v['order_id']);
            $order_list[$k]['goods_list'] = $data;
        }
        $res_data['total'] = $total;
        $res_data['page'] = $page;
        $res_data['page_count'] = ceil($total / $limit);
        $res_data['limit'] = $limit;
        //订单状态对应的中文描述
        $res_data['order_status'] = OrderService::ORDER_STATUS();
        //订单物流状态对应的中文描述
        $res_data['shipping_status'] = OrderService::SHIPPING_STATUS();
        //订单支付状态
        $res_data['pay_status'] = OrderService::PAY_STATUS();
        //订单列表
        $res_data['lists'] = $order_list;
        $this->success($res_data, '', true);
    }

    /**
     * 根据购物车商品下单
     */
    public function create_order_by_cart() {
        $address_id = I("address_id", 0); //  收货地址id
        $invoice_title = I('invoice_title'); // 发票抬头
        $pay_points = I("pay_points", 0); //  使用积分
        $user_money = I("user_money", 0); //  使用余额
        $coupon_price = 0; //优惠价格，默认为没有使用优惠券

        //检测是否使用优惠券
        if(I('usercoupon_id')){
            $coupon_info = CouponService::getUserCouponInfo(I('usercoupon_id'),$this->userid);
            $coupon_price = $coupon_info['discount_price'];
        }

        $where_cart['userid'] = $this->userid;
        $where_cart['id'] = array('in', I('cart_ids'));
        $order_goods = M('Cart')->where($where_cart)->select();
        //检测购物车是否有选择商品
        if (count($order_goods) == 0) {
            $this->error('你的购物车没有选中商品');
        } // 返回结果状态
        //检测地址是否存在
        $address = M('UserAddress')->where("address_id = '%d'", $address_id)->find();
        if (!$address || !$address_id) {
            $this->error('请先填写收货人信息');
        } // 返回结果状态

        $order_service = new OrderService();

        //按选中购物车的商品，计算出各个部分的价格
        $result = $order_service->calculate_price($this->userid, $order_goods, 0, $pay_points, $user_money, $coupon_price);
        if (!$result) {
            $this->error($order_service->get_err_msg());
        }

        $cart_price = array(
            'postFee' => $result['shipping_price'], // 物流费
            'couponFee' => $result['coupon_price'], // 优惠券
            'balance' => $result['user_money'], // 使用用户余额
            'pointsFee' => $result['integral_money'], // 积分支付
            'payables' => $result['order_amount'], // 应付金额
            'goodsFee' => $result['goods_price'],// 商品价格
            'order_prom_id' => $result['order_prom_id'], // 订单优惠活动id
            'order_prom_amount' => $result['order_prom_amount'], // 订单优惠活动优惠了多少钱
        );
        $result = $order_service->addOrder($this->userid, $order_goods, $address_id, '', $invoice_title, 0,
            $cart_price); // 添加订单
        if ($result) {
            $update = [
                'pay_code' => I('post.pay_code', 'cod'), //默认是现金支付
            ];
            $res = $order_service->updateInfo($result, $update);
            if ($res) {
                $this->success($result);
            } else {
                $this->error('更新订单失败');
            }
        } else {
            $this->error($order_service->get_err_msg());
        }
    }

    /**
     * 订单发货记录
     */
    public function order_delivery() {
        $order_sn = I('get.order_sn');
        $deliverys = M('DeliveryDoc')->where(['order_sn' => $order_sn])->select();
        $this->success($deliverys ? $deliverys : [], '', true);
    }
}
