<?php


namespace Shop\Logic;

use Common\Model\RelationModel;
use Shop\Service\DeliveryService;
use Shop\Service\GoodsService;
use Shop\Service\OrderService;

class OrderLogic extends RelationModel {
    /**
     * @param array  $condition 搜索条件
     * @param string $order     排序方式
     * @param int    $page      获取页数
     * @param int    $limit     获取数量
     * @return array
     */
    public function getOrderList($condition, $order = '', $page = 1, $limit = 20) {
        $res = M('order')->where($condition)->page($page, $limit)->order($order)->select();

        return $res ? $res : [];
    }

    /*
     * 获取订单商品详情
     */
    public function getOrderGoods($order_id) {
        $sql = "SELECT g.*,o.*,(o.goods_num * o.member_goods_price) AS goods_total FROM __PREFIX__order_goods o " . "LEFT JOIN __PREFIX__goods g ON o.goods_id = g.goods_id WHERE o.order_id = $order_id";
        $res = $this->query($sql);

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

        return M('order_action')->add($data);//订单操作记录
    }

    /**
     * 获取订单商品总价格
     *
     * @param $order_id
     * @return mixed
     */
    public function getGoodsAmount($order_id) {
        $sql = "SELECT SUM(goods_num * goods_price) AS goods_amount FROM __PREFIX__order_goods WHERE order_id = {$order_id}";
        $res = $this->query($sql);

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
        $order_service = new OrderService();
        $res = null;
        switch ($act) {
            case 'pay': //付款
                $res = $order_service->payOrder($order_sn);

                break;
            case 'pay_cancel': //取消付款
                $res = $order_service->cancelPay($order_sn);

                break;
            case 'confirm': //确认订单
                $res = $order_service->confirmOrder($order_sn);

                break;
            case 'cancel': //取消确认
                $res = $order_service->cancelOrder($order_sn);

                break;
            case 'invalid': //作废订单
                $res = $order_service->invalidOrder($order_sn);

                break;
            case 'delivery_confirm'://确认收货
                $res = $order_service->deliveryOrder($order_sn);

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
                $r = M('order_goods')->where("rec_id=" . $v['rec_id'])->save($res);//改变订单商品发货状态
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
    function delOrder($order_id) {
        $order = M(OrderService::TABLE_NAME)->where(array('order_id' => $order_id))->find();
        if ($order['order_status'] == 3 || $order['order_status'] == 5) {
            return false;
        }
        $a = M(OrderService::TABLE_NAME)->where(array('order_id' => $order_id))->delete();
        $b = M('order_goods')->where(array('order_id' => $order_id))->delete();

        return $a && $b;
    }

}