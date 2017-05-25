<?php
namespace Shop\Service;

use Record\Model\RecordModel;
use Record\Service\TradeRecordService;
use Shop\Model\OrderModel;
use Think\Hook;

class OrderService extends BaseService {
    //定义订单表
    const TABLE_NAME = 'ShopOrder';

    //定义退回订单表
    const RETURN_TABLE_NAME = 'ShopReturnGoods';

    //定义订单跟进表
    const ORDER_ACTION_TABLE_NAME = 'ShopOrderAction';

    //定义订单商品表
    const ORDER_GOODS_TABLE_NAME = 'ShopOrderGoods';

    /**
     * 订单状态
     */
    static function ORDER_STATUS() {
        return array(
            0 => '待确认',
            1 => '已确认',
            2 => '已收货',
            3 => '已取消',
            4 => '已完成',
            5 => '已作废',
        );
    }

    /**
     * 支付状态
     */
    static function PAY_STATUS() {
        return array(
            0 => '未支付',
            1 => '已支付',
        );
    }

    /**
     * 支付方式
     */
    static function PAY_WAY() {
        return array(
            'alipay' => '支付宝支付',
            'wxpay' => '微信支付',
            'cod' => '货到付款'
        );
    }

    /**
     * 发货状态
     */
    static function SHIPPING_STATUS() {
        return array(
            0 => '未发货',
            1 => '已发货',
            2 => '部分发货'
        );
    }

    /**
     *  添加一个订单
     *
     * @param string     $user_id       用户id
     * @param array      $cartList      选中购物车商品
     * @param string     $address_id    地址id
     * @param string     $shipping_code 物流编号
     * @param string     $invoice_title 发票
     * @param string|int $coupon_id     优惠券id
     * @param array      $cart_price    各种价格
     * @return string $order_id 返回新增的订单id
     */
    public function addOrder(
        $user_id,
        $cartList,
        $address_id,
        $shipping_code,
        $invoice_title,
        $coupon_id = 0,
        $cart_price
    ) {
        // 0插入订单 order
        $address = M(UserService::ADDRESS_TABLE_NAME)->where("address_id = $address_id")->find();
        $shipping = [];
        $data = array(
            'order_sn' => date('YmdHis') . rand(1000, 9999), // 订单编号
            'user_id' => $user_id, // 用户id
            'consignee' => $address['consignee'], // 收货人
            'province' => $address['province'],//'省份id',
            'city' => $address['city'],//'城市id',
            'district' => $address['district'],//'县',
            'twon' => $address['twon'],// '街道',
            'address' => $address['address'],//'详细地址',
            'mobile' => $address['mobile'],//'手机',
            'zipcode' => $address['zipcode'],//'邮编',
            'email' => $address['email'],//'邮箱',
            'shipping_code' => $shipping_code,//'物流编号',
            'shipping_name' => $shipping['name'], //'物流名称',
            'invoice_title' => $invoice_title, //'发票抬头',
            'goods_price' => $cart_price['goodsFee'],//'商品价格',
            'shipping_price' => $cart_price['postFee'],//'物流价格',
            'user_money' => $cart_price['balance'],//'使用余额',
            'coupon_price' => $cart_price['couponFee'],//'使用优惠券',
            'integral' => ($cart_price['pointsFee'] * tpCache('shopping.point_rate')), //'使用积分',
            'integral_money' => $cart_price['pointsFee'],//'使用积分抵多少钱',
            'total_amount' => ($cart_price['goodsFee'] + $cart_price['postFee']),// 订单总额
            'order_amount' => $cart_price['payables'],//'应付款金额',
            'add_time' => time(), // 下单时间
            'order_prom_id' => $cart_price['order_prom_id'],//'订单优惠活动id',
            'order_prom_amount' => $cart_price['order_prom_amount'],//'订单优惠活动优惠了多少钱',
            'discount' => $cart_price['discount'],//'优惠券抵扣价格'
        );

        $order_id = M(self::TABLE_NAME)->data($data)->add();
        if (!$order_id) {
            $this->set_err_msg('添加订单失败');

            return false;
        }
        // 记录订单操作日志
        logOrder($order_id, '您提交了订单，请等待系统确认', '提交订单', $user_id);
        $order = M(self::TABLE_NAME)->where("order_id = $order_id")->find();
        // 1插入order_goods 表
        $order_goods_ids = array();
        foreach ($cartList as $key => $val) {
            $order_goods_ids[] = $val['goods_id'];
            $goods = M(GoodsService::GOODS_TABLE_NAME)->where(['goods_id' => $val['goods_id']])->find();
            $order_goods['order_id'] = $order_id; // 订单id
            $order_goods['goods_id'] = $val['goods_id']; // 商品id
            $order_goods['goods_name'] = $val['goods_name']; // 商品名称
            $order_goods['goods_sn'] = $val['goods_sn']; // 商品货号
            $order_goods['goods_num'] = $val['goods_num']; // 购买数量
            $order_goods['market_price'] = $val['market_price']; // 市场价
            $order_goods['goods_price'] = $val['goods_price']; // 商品价
            $order_goods['spec_key'] = $val['spec_key']; // 商品规格
            $order_goods['spec_key_name'] = $val['spec_key_name']; // 商品规格名称
            $order_goods['sku'] = $val['sku']; // 商品sku
            $order_goods['member_goods_price'] = $val['member_goods_price']; // 会员折扣价
            $order_goods['cost_price'] = $goods['cost_price']; // 成本价
            $order_goods['give_integral'] = $goods['give_integral']; // 购买商品赠送积分
            $order_goods['prom_type'] = $val['prom_type']; // 0 普通订单,1 限时抢购, 2 团购 , 3 促销优惠
            $order_goods['prom_id'] = $val['prom_id']; // 活动id
            M(self::ORDER_GOODS_TABLE_NAME)->data($order_goods)->add();
            //扣除商品库存
            if ($val['spec_key']) {
                //如果存在sku库存
                M('ShopSpecGoodsPrice')->where("`goods_id`='%d' AND `key`='%s' ", $val['goods_id'],
                    $val['spec_key'])->setDec('store_count', $val['goods_num']);
            } else {
                M(GoodsService::GOODS_TABLE_NAME)->where("goods_id = " . $val['goods_id'])->setDec('store_count', $val['goods_num']); // 商品减少库存
            }
        }

        //优惠券抵扣
        if ($coupon_id) {
            //将优惠券的状态修改成 已使用
            CouponService::useCoupon($coupon_id, $user_id, $order['order_sn'], 'order',
                CouponService::COUPON_STATUS_ISUSE);
        }
        //TODO 扣除积分 扣除余额
        //TODO 抵扣余额
        if ($cart_price['balance'] > 0) {
            //使用后的余额进行抵扣
            TradeRecordService::createTradeRecord($user_id, 'pay_order', $order['order_sn'], 0, $cart_price['balance'],
                RecordModel::STATUS_VAILD, '购买商品抵扣');
        }
        // 4 删除已提交订单商品

        $where = array('userid' => $user_id, 'goods_id' => array('in', $order_goods_ids));
        M(CartService::TABLE_NAME)->where($where)->delete();


        // 如果应付金额为0  可能是余额支付 + 积分 + 优惠券 这里订单支付状态直接变成已支付
        if ($data['order_amount'] == 0) {
            update_pay_status($order['order_sn'], 1);
        }

        return $order_id;
    }

    /**
     * 获取订单商品
     *
     * @param $order_id
     * @return mixed
     */
    static function get_order_goods($order_id) {
//        $sql = "SELECT og.*,g.original_img FROM __PREFIX__order_goods og LEFT JOIN __PREFIX__goods g ON g.goods_id = og.goods_id WHERE order_id = " . $order_id;
        $goods_list = M(self::ORDER_GOODS_TABLE_NAME)->alias('og')->field('og.*,g.original_img')->join(C('DB_PREFIX') . "shop_goods g ON g.goods_id = og.goods_id")->where("order_id='%d'",
            $order_id)->select();

        return $goods_list ? $goods_list : [];
    }

    /**
     * 计算订单的价格
     *
     * @param int $userid
     * @param     $order_goods
     * @param int $shipping_price
     * @param int $pay_points
     * @param int $user_money
     * @param int $coupon_id
     * @return array|bool
     */
    public function calculate_price(
        $userid = 0,
        $order_goods,
        $shipping_price = 0,
        $pay_points = 0,
        $user_money = 0,
        $coupon_id = 0
    ) {
        if (empty($order_goods)) {
            $this->set_err_msg('商品列表不能为空');

            return false;
        }

        //检测是否使用优惠券
        if ($coupon_id) {
            $coupon_res = CouponService::getUserCouponInfo(I('usercoupon_id'), $userid);
            if ($coupon_res['status']) {
                $this->set_err_msg($coupon_res['msg']);

                return false;
            }
            $coupon_info = $coupon_res['data'];
            $coupon_price = $coupon_info['discount_price'];
        } else {
            $coupon_price = 0;
        }

        // 检查账户余额的情况
        if ($user_money) {
            $user_service = new UserService();
            $balance = $user_service->getBalance($userid);
            if ($balance < $user_money) {
                //余额不足
                $this->set_err_msg('余额不足');

                return false;
            }
        }

        $goods_id_arr = get_arr_column($order_goods, 'goods_id');
        $goods_arr = M(GoodsService::GOODS_TABLE_NAME)->where("goods_id in(" . implode(',',
                $goods_id_arr) . ")")->getField('goods_id,weight,market_price,is_free_shipping'); // 商品id 和重量对应的键值对

        $goods_weight = 0;
        $goods_price = 0;
        foreach ($order_goods as $key => $val) {

            //如果商品不是包邮的
            if ($goods_arr[$val['goods_id']]['is_free_shipping'] == 0) {
                $goods_weight += $goods_arr[$val['goods_id']]['weight'] * $val['goods_num'];
            }
            //累积商品重量 每种商品的重量 * 数量

            $order_goods[$key]['goods_fee'] = $val['goods_num'] * $val['member_goods_price']; // 小计
            $order_goods[$key]['store_count'] = getGoodNum($val['goods_id'], $val['spec_key']); // 最多可购买的库存数量
            if ($order_goods[$key]['store_count'] <= 0) {
                $this->set_err_msg('库存不足,请重新下单');

                return false;
            }

            $goods_price += $order_goods[$key]['goods_fee']; // 商品总价
            $cut_fee = 0;
            $cut_fee += $val['goods_num'] * $val['market_price'] - $val['goods_num'] * $val['member_goods_price']; // 共节约
            $anum = 0;
            $anum += $val['goods_num']; // 购买数量
        }


        // 返回结果状态

        $order_amount = $goods_price + $shipping_price; // 应付金额 = 商品价格 + 物流费

        // TODO 积分抵扣暂时不涉及 $pay_points = 0;
        $pay_points = ($pay_points > $order_amount) ? $order_amount : $pay_points; // 假设应付 1块钱 而用户输入了 200 积分 2块钱, 那么就让 $pay_points = 1块钱 等同于强制让用户输入1块钱
        $order_amount = $order_amount - $pay_points; //  积分抵消应付金额

        $user_money = ($user_money > $order_amount) ? $order_amount : $user_money; // 余额支付原理等同于积分
        $order_amount = $order_amount - $user_money; //  余额支付抵应付金额

        $coupon_price = ($coupon_price > $order_amount) ? $order_amount : $coupon_price; //优惠价格
        $order_amount = $order_amount - $coupon_price;// 优惠券抵扣应付金额

        $total_amount = $goods_price + $shipping_price;
        //订单总价  应付金额  物流费  商品总价 节约金额 共多少件商品 积分  余额  优惠券
        $result = array(
            'total_amount' => $total_amount, // 商品总价
            'order_amount' => $order_amount, // 应付金额
            'shipping_price' => $shipping_price, // 物流费
            'goods_price' => $goods_price, // 商品总价
            'cut_fee' => $cut_fee, // 共节约多少钱
            'anum' => $anum, // 商品总共数量
            'integral_money' => $pay_points, // 积分抵消金额
            'user_money' => $user_money, // 使用余额
            'coupon_price' => $coupon_price, // 优惠券抵消金额
            'order_goods' => $order_goods, // 商品列表 多加几个字段原样返回
        );

        return $result;
    }


    public function updateInfo($order_id, $data) {
        $res = M(self::TABLE_NAME)->where(['order_id' => $order_id])->save($data);

        return $res;
    }

    /**
     * 支付成功
     *
     * @param $order_sn
     * @return bool
     */
    public function payOrder($order_sn) {
        $order = M(self::TABLE_NAME)->where(['order_sn' => $order_sn])->find();
        if ($order) {
            if ($order['pay_status'] == OrderModel::PAY_STATUS_NO) {
                $update = [
                    'pay_status' => OrderModel::PAY_STATUS_YES,
                    'pay_time' => time()
                ];
                M(self::TABLE_NAME)->where(['order_sn' => $order_sn])->save($update);
                self::logOrder($order['order_id'], '订单支付成功', '支付成功', $order['user_id']);
                //支付成功后调用支付成功hook
                Hook::listen('shop_order_pay', $order);

                return $order['order_id'];
            } else {
                //已经支付
                $this->set_err_msg('该订单已经支付');

                return false;
            }
        } else {
            $this->set_err_msg('找不到该订单');

            return false;
        }
    }

    /**
     * 取消支付
     *
     * @param $order_sn
     * @return bool
     */
    public function cancelPay($order_sn) {
        $order = M(self::TABLE_NAME)->where(['order_sn' => $order_sn])->find();
        if ($order) {
            if ($order['pay_status'] == OrderModel::PAY_STATUS_YES) {
                $update = [
                    'pay_status' => OrderModel::PAY_STATUS_NO,
                    'pay_time' => time()
                ];
                M(self::TABLE_NAME)->where(['order_sn' => $order_sn])->save($update);
                self::logOrder($order['order_id'], '订单取消支付', '支付取消', $order['user_id']);

                return $order['order_id'];
            } else {
                //该订单未支付
                $this->set_err_msg('该订单未支付');

                return false;
            }
        } else {
            $this->set_err_msg('找不到该订单');

            return false;
        }
    }

    /**
     * 确认订单
     *
     * @param $order_sn
     * @return bool
     */
    public function confirmOrder($order_sn) {
        $order = M(self::TABLE_NAME)->where(['order_sn' => $order_sn])->find();
        if ($order) {
            $update = [
                'order_status' => OrderModel::STATUS_CONFIRM,
                'update_time' => time()
            ];
            M(self::TABLE_NAME)->where(['order_sn' => $order_sn])->save($update);
            self::logOrder($order['order_id'], '订单确认', '订单确认', $order['user_id']);

            return true;
        } else {
            $this->set_err_msg('找不到该订单');

            return false;
        }
    }

    /**
     *  取消订单操作
     *
     * @param $order_sn
     * @return bool
     */
    public function cancelOrder($order_sn) {
        $order = M(self::TABLE_NAME)->where(['order_sn' => $order_sn])->find();
        if ($order) {
            $update = [
                'order_status' => OrderModel::STATUS_CANCEL,
                'update_time' => time()
            ];
            M(self::TABLE_NAME)->where(['order_sn' => $order_sn])->save($update);
            //取消订单，处理下单的商品的库存问题
            $goods_list = M(self::ORDER_GOODS_TABLE_NAME)->where(['order_id' => $order['order_id']])->select();
            foreach ($goods_list as $key => $value) {
                if ($value['spec_key']) {
                    //如果是有规格
                    M('ShopSpecGoodsPrice')->where([
                        'goods_id' => $value['goods_id'],
                        'key' => $value['spec_key']
                    ])->setInc('store_count', $value['goods_num']);
                } else {
                    //没有规格商品,增加库存
                    M(GoodsService::GOODS_TABLE_NAME)->where(['goods_id' => $value['goods_id']])->setInc('store_count', $value['goods_num']);
                }
                M(GoodsService::GOODS_TABLE_NAME)->where(['goods_id' => $value['goods_id']])->setDec('sales_sum', $value['goods_num']);
            }
            self::logOrder($order['order_id'], '订单取消', '订单取消', $order['user_id']);

            return true;
        } else {
            $this->set_err_msg('找不到该订单');

            return false;
        }
    }

    /**
     * 订单无效操作
     *
     * @param $order_sn
     * @return bool
     */
    public function invalidOrder($order_sn) {
        $order = M(self::TABLE_NAME)->where(['order_sn' => $order_sn])->find();
        if ($order) {
            if ($order['order_status'] == OrderModel::STATUS_CANCEL) {
                $this->set_err_msg('请先取消订单');

                return false;
            }
            $update = [
                'order_status' => OrderModel::STATUS_INVALID,
                'update_time' => time()
            ];
            M(self::TABLE_NAME)->where(['order_sn' => $order_sn])->save($update);

            self::logOrder($order['order_id'], '作废订单', '作废订单', $order['user_id']);

            return true;
        } else {
            $this->set_err_msg('找不到该订单');

            return false;
        }
    }

    /**
     * 确认收货操作
     *
     * @param $order_sn
     * @return bool
     */
    public function deliveryOrder($order_sn) {
        $order = M(self::TABLE_NAME)->where(['order_sn' => $order_sn])->find();
        if ($order) {
            $update = [
                'order_status' => OrderModel::STATUS_SHIPPING,
                'update_time' => time()
            ];
            M(self::TABLE_NAME)->where(['order_sn' => $order_sn])->save($update);
            self::logOrder($order['order_id'], '确认收货', '确认收货', $order['user_id']);
            Hook::listen('shop_order_delivery', $order);

            return true;
        } else {
            $this->set_err_msg('找不到该订单');

            return false;
        }
    }

    static function logOrder($order_id, $action_note, $status_desc, $user_id = 0) {
        $order = M(self::TABLE_NAME)->where("order_id = $order_id")->find();
        $action_info = array(
            'order_id' => $order_id,
            'action_user' => $user_id,
            'order_status' => $order['order_status'],
            'shipping_status' => $order['shipping_status'],
            'pay_status' => $order['pay_status'],
            'action_note' => $action_note,
            'status_desc' => $status_desc, //''
            'log_time' => time(),
        );

        return M(OrderService::ORDER_ACTION_TABLE_NAME)->add($action_info);
    }

    /**
     * @param array  $condition 搜索条件
     * @param string $order     排序方式
     * @param int    $page      获取页数
     * @param int    $limit     获取数量
     * @return array
     */
    public function getOrderList($condition, $order = '', $page = 1, $limit = 20) {
        $res = M(self::TABLE_NAME)->where($condition)->page($page, $limit)->order($order)->select();

        return $res ? $res : [];
    }

    /*
     * 获取订单商品详情
     */
    public function getOrderGoods($order_id) {
        $sql = "SELECT g.*,o.*,(o.goods_num * o.member_goods_price) AS goods_total FROM __PREFIX__shop_order_goods o " . "LEFT JOIN __PREFIX__shop_goods g ON o.goods_id = g.goods_id WHERE o.order_id = $order_id";
        $res = M()->query($sql);

        return $res;
    }

    /**
     * 获取订单信息
     *
     * @param $order_id
     * @return mixed
     */
    public function getOrderInfo($order_id) {
        //  订单总金额查询语句
        $order = M(OrderService::TABLE_NAME)->where("order_id = $order_id")->find();
        $order['address2'] = $this->getAddressName($order['province'], $order['city'], $order['district']);
        $order['address2'] = $order['address2'] . $order['address'];

        return $order;
    }

    /**
     * 根据商品型号获取商品
     *
     * @param $goods_id_arr
     * @return array|bool
     */
    public function get_spec_goods($goods_id_arr) {
        if (!is_array($goods_id_arr)) {
            return false;
        }
        foreach ($goods_id_arr as $key => $val) {
            $arr = array();
            $goods = M(GoodsService::GOODS_TABLE_NAME)->where("goods_id = $key")->find();
            $arr['goods_id'] = $key; // 商品id
            $arr['goods_name'] = $goods['goods_name'];
            $arr['goods_sn'] = $goods['goods_sn'];
            $arr['market_price'] = $goods['market_price'];
            $arr['goods_price'] = $goods['shop_price'];
            $arr['cost_price'] = $goods['cost_price'];
            $arr['member_goods_price'] = $goods['shop_price'];
            foreach ($val as $k => $v) {
                $arr['goods_num'] = $v['goods_num']; // 购买数量
                // 如果这商品有规格
                if ($k != 'key') {
                    $arr['spec_key'] = $k;
                    $spec_goods = M('shop_spec_goods_price')->where("goods_id = $key and `key` = '{$k}'")->find();
                    $arr['spec_key_name'] = $spec_goods['key_name'];
                    $arr['member_goods_price'] = $arr['goods_price'] = $spec_goods['price'];
                    $arr['sku'] = $spec_goods['sku']; // 参考 sku  http://www.zhihu.com/question/19841574
                }
                $order_goods[] = $arr;
            }
        }

        return $order_goods;
    }

    /**
     * 订单操作记录
     *
     * @param string $order_id
     * @param string $action
     * @param string $note
     * @return mixed
     */
    public function orderActionLog($order_id, $action, $note = '') {
        $order = M(OrderService::TABLE_NAME)->where(array('order_id' => $order_id))->find();
        $data['order_id'] = $order_id;
        $data['action_user'] = session('admin_id');
        $data['action_note'] = $note;
        $data['order_status'] = $order['order_status'];
        $data['pay_status'] = $order['pay_status'];
        $data['shipping_status'] = $order['shipping_status'];
        $data['log_time'] = time();
        $data['status_desc'] = $action;

        return M(OrderService::ORDER_ACTION_TABLE_NAME)->add($data);//订单操作记录
    }

    /**
     * 获取订单商品总价格
     *
     * @param $order_id
     * @return mixed
     */
    public function getGoodsAmount($order_id) {
        $sql = "SELECT SUM(goods_num * goods_price) AS goods_amount FROM __PREFIX__shop_order_goods WHERE order_id = {$order_id}";
        $res = M()->query($sql);

        return $res[0]['goods_amount'];
    }

    /**
     * 得到发货单流水号
     */
    public function get_delivery_sn() {
        /* 选择一个随机的方案 */
        send_http_status('310');
        mt_srand((double)microtime() * 1000000);

        return date('YmdHi') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    /**
     * 获取当前可操作的按钮
     *
     * @param $order
     * @return array
     */
    public function getOrderButton($order) {
        /*
         *  操作按钮汇总 ：付款、设为未付款、确认、取消确认、无效、去发货、确认收货、申请退货
         *
         */
        $os = $order['order_status'];//订单状态
        $ss = $order['shipping_status'];//发货状态
        $ps = $order['pay_status'];//支付状态
        $btn = array();
        if ($order['pay_code'] == 'cod') {
            if ($os == 0 && $ss == 0) {
                $btn['confirm'] = '确认';
            } elseif ($os == 1 && $ss == 0) {
                $btn['delivery'] = '去发货';
                $btn['cancel'] = '取消确认';
            } elseif ($ss == 1 && $os == 1 && $ps == 0) {
                $btn['pay'] = '付款';
            } elseif ($ps == 1 && $ss == 1 && $os == 1) {
                $btn['pay_cancel'] = '设为未付款';
            }
        } else {
            if ($ps == 0 && $os == 0) {
                $btn['pay'] = '付款';
            } elseif ($os == 0 && $ps == 1) {
                $btn['pay_cancel'] = '设为未付款';
                $btn['confirm'] = '确认';
            } elseif ($os == 1 && $ps == 1 && $ss == 0) {
                $btn['cancel'] = '取消确认';
                $btn['delivery'] = '去发货';
            }
        }

        if ($ss == 1 && $os == 1 && $ps == 1) {
            $btn['delivery_confirm'] = '确认收货';
            $btn['refund'] = '申请退货';
        } elseif ($os == 2 || $os == 4) {
            $btn['refund'] = '申请退货';
        } elseif ($os == 3 || $os == 5) {
            $btn['remove'] = '移除';
        }
        if ($os != 5) {
            $btn['invalid'] = '无效';
        }

        return $btn;
    }

    /**
     * @param string $order_id
     * @param string $act
     * @return bool
     */
    public function orderProcessHandle($order_id, $act) {
        $order_sn = M(OrderService::TABLE_NAME)->where("order_id = $order_id")->getField("order_sn");
//        $order_service = new OrderService();
        $res = null;
        switch ($act) {
            case 'pay': //付款
                $res = $this->payOrder($order_sn);

                break;
            case 'pay_cancel': //取消付款
                $res = $this->cancelPay($order_sn);

                break;
            case 'confirm': //确认订单
                $res = $this->confirmOrder($order_sn);

                break;
            case 'cancel': //取消确认
                $res = $this->cancelOrder($order_sn);

                break;
            case 'invalid': //作废订单
                $res = $this->invalidOrder($order_sn);

                break;
            case 'delivery_confirm'://确认收货
                $res = $this->deliveryOrder($order_sn);

                break;
            default:
                return true;
        }
        if ($res) {
            return true;
        } else {
            return false;
        }
    }


    //管理员取消付款
    function order_pay_cancel($order_id) {

        //如果这笔订单已经取消付款过了
        $count = M(OrderService::TABLE_NAME)->where("order_id = $order_id and pay_status = 1")->count();   // 看看有没已经处理过这笔订单  支付宝返回不重复处理操作
        if ($count == 0) {
            return false;
        }
        // 找出对应的订单
        $order = M(OrderService::TABLE_NAME)->where("order_id = $order_id")->find();
        // 增加对应商品的库存
        $orderGoodsArr = M('OrderGoods')->where("order_id = $order_id")->select();
        foreach ($orderGoodsArr as $key => $val) {
            if (!empty($val['spec_key']))// 有选择规格的商品
            {   // 先到规格表里面增加数量 再重新刷新一个 这件商品的总数量
                M('ShopSpecGoodsPrice')->where("goods_id = {$val['goods_id']} and `key` = '{$val['spec_key']}'")->setInc('store_count',
                    $val['goods_num']);
                refresh_stock($val['goods_id']);
            } else {
                M(GoodsService::GOODS_TABLE_NAME)->where("goods_id = {$val['goods_id']}")->setInc('store_count', $val['goods_num']); // 增加商品总数量
            }
            M(GoodsService::GOODS_TABLE_NAME)->where("goods_id = {$val['goods_id']}")->setDec('sales_sum', $val['goods_num']); // 减少商品销售量
            //更新活动商品购买量
            if ($val['prom_type'] == 1 || $val['prom_type'] == 2) {
                $prom = get_goods_promotion($val['goods_id']);
                if ($prom['is_end'] == 0) {
                    $tb = $val['prom_type'] == 1 ? 'flash_sale' : 'group_buy';
                    M($tb)->where("id=" . $val['prom_id'])->setDec('buy_num', $val['goods_num']);
                    M($tb)->where("id=" . $val['prom_id'])->setDec('order_num');
                }
            }
        }
        // 根据order表查看消费记录 给他会员等级升级 修改他的折扣 和 总金额
        M(OrderService::TABLE_NAME)->where("order_id=$order_id")->save(array('pay_status' => 0));
        update_user_level($order['user_id']);
        // 记录订单操作日志
        logOrder($order['order_id'], '订单取消付款', '付款取消', $order['user_id']);
        //分销设置
        M('rebate_log')->where("order_id = {$order['order_id']}")->save(array('status' => 0));
    }

    /**
     *    处理发货单
     *
     * @param array $data 查询数量
     * @return boolean
     */
    public function deliveryHandle($data) {
        $order = $this->getOrderInfo($data['order_id']);
        $orderGoods = $this->getOrderGoods($data['order_id']);
        $selectgoods = $data['goods'];
        $data['order_sn'] = $order['order_sn'];
        $data['delivery_sn'] = $this->get_delivery_sn();
        $data['zipcode'] = $order['zipcode'];
        $data['user_id'] = $order['user_id'];
        $data['admin_id'] = session('admin_id');
        $data['consignee'] = $order['consignee'];
        $data['mobile'] = $order['mobile'];
        $data['country'] = $order['country'];
        $data['province'] = $order['province'];
        $data['city'] = $order['city'];
        $data['district'] = $order['district'];
        $data['address'] = $order['address'];
//        $data['shipping_code'] = $data['shipping_code'];
//        $data['shipping_name'] = $data['shipping_name'];
        $data['shipping_price'] = $order['shipping_price'];
        $data['create_time'] = time();
        $did = M(DeliveryService::TABLE_NAME)->add($data);
        $is_delivery = 0;
        foreach ($orderGoods as $k => $v) {
            if ($v['is_send'] == 1) {
                $is_delivery++;
            }
            if ($v['is_send'] == 0 && in_array($v['rec_id'], $selectgoods)) {
                $res['is_send'] = 1;
                $res['delivery_id'] = $did;
                $r = M(self::ORDER_GOODS_TABLE_NAME)->where("rec_id=" . $v['rec_id'])->save($res);//改变订单商品发货状态
                $is_delivery++;
            }
        }
        $updata['shipping_time'] = time();
        $updata['shipping_code'] = $data['shipping_code'];
        $updata['shipping_name'] = $data['shipping_name'];
        if ($is_delivery == count($orderGoods)) {
            $updata['shipping_status'] = 1;
        } else {
            $updata['shipping_status'] = 2;
        }
        M(OrderService::TABLE_NAME)->where("order_id=" . $data['order_id'])->save($updata);//改变订单状态
        $s = $this->orderActionLog($order['order_id'], 'delivery', $data['note']);//操作日志
        return $s && $r;
    }

    /**
     * 获取地区名字
     *
     * @param int $p
     * @param int $c
     * @param int $d
     * @return string
     */
    public function getAddressName($p = 0, $c = 0, $d = 0) {
        $p = M('AreaProvince')->where(array('id' => $p))->field('areaname')->find();
        $c = M('AreaCity')->where(array('id' => $c))->field('areaname')->find();
        $d = M('AreaDistrict')->where(array('id' => $d))->field('areaname')->find();

        return $p['areaname'] . ',' . $c['areaname'] . ',' . $d['areaname'] . ',';
    }

    /**
     * 删除订单
     *
     * @param $order_id
     * @return bool
     */
    public function delOrder($order_id) {
        $order = M(self::TABLE_NAME)->where(array('order_id' => $order_id))->find();
        if ($order['order_status'] == 3 || $order['order_status'] == 5) {
            return false;
        }
        $a = M(self::TABLE_NAME)->where(array('order_id' => $order_id))->delete();
        $b = M(self::ORDER_GOODS_TABLE_NAME)->where(array('order_id' => $order_id))->delete();

        return $a && $b;
    }
}