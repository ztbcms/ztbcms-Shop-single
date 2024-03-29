<?php

namespace Shop\Controller;

use Shop\Logic\OrderLogic;
use Shop\Logic\CartLogic;
use Shop\Service\BrandService;
use Shop\Service\CategoryService;
use Shop\Service\DeliveryService;
use Shop\Service\GoodsService;
use Shop\Service\OrderService;
use Shop\Util\AjaxPage;
use Common\Controller\AdminBase;


class OrderController extends AdminBase {
    public $order_status;
    public $pay_status;
    public $shipping_status;

    /*
     * 初始化操作
     */
    public function _initialize() {
        parent::_initialize();
        C('TOKEN_ON', false); // 关闭表单令牌验证
        $this->order_status = C('ORDER_STATUS');
        $this->pay_status = C('PAY_STATUS');
        $this->shipping_status = C('SHIPPING_STATUS');
        // 订单 支付 发货状态
        $this->assign('order_status', $this->order_status);
        $this->assign('pay_status', $this->pay_status);
        $this->assign('shipping_status', $this->shipping_status);
    }

    /**
     * 订单首页
     */
    public function index() {
        $begin = date('Y/m/d', (time() - 30 * 60 * 60 * 24));//30天前
        $end = date('Y/m/d', strtotime('+1 days'));
        $this->assign('timegap', $begin . '-' . $end);
        $this->display();
    }

    /**
     * 订单列表接口
     */
    public function orderList() {
        $orderService = new OrderService();
        $timegap = I('timegap');
        $begin = $end = null;
        if ($timegap) {
            $gap = explode('-', $timegap);
            $begin = strtotime($gap[0]);
            $end = strtotime($gap[1]);
        }
        // 搜索条件
        $condition = array();
        I('consignee') ? $condition['consignee'] = ['like', '%' . trim(I('consignee')) . '%'] : false;
        if ($begin && $end) {
            $condition['add_time'] = array('between', "$begin,$end");
        }
        I('order_sn') ? $condition['order_sn'] = ['like', '%' . trim(I('order_sn')) . '%'] : false;
        I('order_status') != '' ? $condition['order_status'] = I('order_status') : false;
        I('pay_status') != '' ? $condition['pay_status'] = I('pay_status') : false;
        I('pay_code') != '' ? $condition['pay_code'] = I('pay_code') : false;
        I('shipping_status') != '' ? $condition['shipping_status'] = I('shipping_status') : false;
        I('user_id') ? $condition['user_id'] = trim(I('user_id')) : false;
        $sort_order = 'order_id desc';

        $page = I('get.page', 1);
        $limit = I('get.limit', 20);
        $total = M(OrderService::TABLE_NAME)->where($condition)->count();
        $page_count = ceil($total / $limit);

        //获取订单列表
        $orderList = $orderService->getOrderList($condition, $sort_order, $page, $limit);

        $res = [
            'lists' => $orderList,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'page_count' => $page_count,
            'order_status' => $this->order_status,
            'pay_status' => $this->pay_status,
            'shipping_status' => $this->shipping_status
        ];


        $this->success($res, '', true);
    }

    /**
     * Ajax首页
     */
    public function ajaxindex() {
        $orderService = new OrderService();
        $timegap = I('timegap');
        if ($timegap) {
            $gap = explode('-', $timegap);
            $begin = strtotime($gap[0]);
            $end = strtotime($gap[1]);
        }
        // 搜索条件
        $condition = array();
        I('consignee') ? $condition['consignee'] = trim(I('consignee')) : false;
        if ($begin && $end) {
            $condition['add_time'] = array('between', "$begin,$end");
        }
        I('order_sn') ? $condition['order_sn'] = trim(I('order_sn')) : false;
        I('order_status') != '' ? $condition['order_status'] = I('order_status') : false;
        I('pay_status') != '' ? $condition['pay_status'] = I('pay_status') : false;
        I('pay_code') != '' ? $condition['pay_code'] = I('pay_code') : false;
        I('shipping_status') != '' ? $condition['shipping_status'] = I('shipping_status') : false;
        I('user_id') ? $condition['user_id'] = trim(I('user_id')) : false;
        $sort_order = I('order_by', 'DESC') . ' ' . I('sort');
        $count = M(OrderService::TABLE_NAME)->where($condition)->count();
        $Page = new AjaxPage($count, 20);
        //  搜索条件下 分页赋值
        foreach ($condition as $key => $val) {
            $Page->parameter[$key] = urlencode($val);
        }
        $show = $Page->show();
        //获取订单列表
        $orderList = $orderService->getOrderList($condition, $sort_order, $Page->firstRow, $Page->listRows);
        $this->assign('orderList', $orderList);
        $this->assign('page', $show);// 赋值分页输出
        $this->display();
    }


    /**
     * 获取发货订单列表接口
     */
    public function getDeliveryList() {
        $condition = array();
        I('consignee') ? $condition['consignee'] = trim(I('consignee')) : false;
        I('order_sn') != '' ? $condition['order_sn'] = trim(I('order_sn')) : false;
        $shipping_status = I('shipping_status');
        $condition['shipping_status'] = empty($shipping_status) ? array('neq', 1) : $shipping_status;
        $condition['order_status'] = array('in', '1,2,4');
        $count = M(OrderService::TABLE_NAME)->where($condition)->count();
        $page = I('page',1);
        $limit = I('limit',10);
        $page_count = ceil ($count / $limit);
        $pageArr = array(
            'page' => $page,
            'page_count' => $page_count,
        );
        $orderList = M(OrderService::TABLE_NAME)->where($condition)->page($page,$limit)->order('add_time DESC')->select();

        $this->ajaxReturn(['orderList'=>$orderList, 'page'=>$pageArr]);
    }

    /**
     * 订单详情
     * @param int $order_id 订单ID
     */
    public function detail($order_id) {

        $orderService = new OrderService();
        $order = $orderService->getOrderInfo($order_id);
        $orderGoods = $orderService->getOrderGoods($order_id);
        $button = $orderService->getOrderButton($order);

        // 获取操作记录
        $action_log = M(OrderService::ORDER_ACTION_TABLE_NAME)->where(array('order_id' => $order_id))->order('log_time desc')->select();
        $this->assign('order', $order);
        $this->assign('action_log', $action_log);
        $this->assign('orderGoods', $orderGoods);
        $split = count($orderGoods) > 1 ? 1 : 0;
        foreach ($orderGoods as $val) {
            if ($val['goods_num'] > 1) {
                $split = 1;
            }
        }
        $this->assign('split', $split);
        $this->assign('button', $button);
        $this->display();
    }


    /**
     * 订单编辑
     */
    public function edit_order() {
        $order_id = I('order_id');
        $orderService = new OrderService();
        $order = $orderService->getOrderInfo($order_id);
        if ($order['shipping_status'] != 0) {
            $this->error('已发货订单不允许编辑');
            exit;
        }

        $orderGoods = $orderService->getOrderGoods($order_id);

        if (IS_POST) {
            $order['consignee'] = I('consignee');// 收货人
            $order['province'] = I('province'); // 省份
            $order['city'] = I('city'); // 城市
            $order['district'] = I('district'); // 县
            $order['address'] = I('address'); // 收货地址
            $order['mobile'] = I('mobile'); // 手机           
            $order['invoice_title'] = I('invoice_title');// 发票
            $order['admin_note'] = I('admin_note'); // 管理员备注
            $order['pay_code'] = I('payment');// 支付方式
            $goods_id_arr = I("goods_id");
            $new_goods = $old_goods_arr = array();
            //################################订单添加商品
            if ($goods_id_arr) {
                $new_goods = $orderService->get_spec_goods($goods_id_arr);
                foreach ($new_goods as $key => $val) {
                    $val['order_id'] = $order_id;
                    $rec_id = M(OrderService::ORDER_GOODS_TABLE_NAME)->add($val);//订单添加商品
                    if (!$rec_id) {
                        $this->error('添加失败');
                    }
                }
            }

            //################################订单修改删除商品
            $old_goods = I('old_goods');
            foreach ($orderGoods as $val) {
                if (empty($old_goods[$val['rec_id']])) {
                    M(OrderService::ORDER_GOODS_TABLE_NAME)->where("rec_id=" . $val['rec_id'])->delete();//删除商品
                } else {
                    //修改商品数量
                    if ($old_goods[$val['rec_id']] != $val['goods_num']) {
                        $val['goods_num'] = $old_goods[$val['rec_id']];
                        M(OrderService::ORDER_GOODS_TABLE_NAME)->where("rec_id=" . $val['rec_id'])->save(array('goods_num' => $val['goods_num']));
                    }
                    $old_goods_arr[] = $val;
                }
            }

            $goodsArr = array_merge($old_goods_arr, $new_goods);
            $result = calculate_price($order['user_id'], $goodsArr, $order['shipping_code'], 0, $order['province'],
                $order['city'], $order['district'], 0, 0, 0, 0);
            if ($result['status'] < 0) {
                $this->error($result['msg']);
            }

            //################################修改订单费用
            $order['goods_price'] = $result['result']['goods_price']; // 商品总价
            $order['shipping_price'] = $result['result']['shipping_price'];//物流费
            $order['order_amount'] = $result['result']['order_amount']; // 应付金额
            $order['total_amount'] = $result['result']['total_amount']; // 订单总价           
            $o = M(OrderService::TABLE_NAME)->where('order_id=' . $order_id)->save($order);

            $l = $orderService->orderActionLog($order_id, 'edit', '修改订单');//操作日志
            if ($o && $l) {
                $this->success('修改成功', U('Order/editprice', array('order_id' => $order_id)));
            } else {
                $this->success('修改失败', U('Order/detail', array('order_id' => $order_id)));
            }
            exit;
        }
        // 获取省份
        $province = M('AreaProvince')->select();
        //获取订单城市
        $city = M('AreaCity')->where(array('parentid' => $order['province'], 'level' => 2))->select();
        //获取订单地区
        $area = M('AreaDistrict')->where(array('parentid' => $order['city'], 'level' => 3))->select();
        //获取支付方式
        $payment_list = OrderService::PAY_WAY();

        $this->assign('order', $order);
        $this->assign('province', $province);
        $this->assign('city', $city);
        $this->assign('area', $area);
        $this->assign('orderGoods', $orderGoods);
        $this->assign('payment_list', $payment_list);
        $this->display();
    }

    /*
     * 拆分订单
     */
    public function split_order() {
        $order_id = I('order_id');
        $orderService = new OrderService();
        $order = $orderService->getOrderInfo($order_id);
        if ($order['shipping_status'] != 0) {
            $this->error('已发货订单不允许编辑');
            exit;
        }
        $orderGoods = $orderService->getOrderGoods($order_id);
        if (IS_POST) {
            $data = I('post.');
            //################################先处理原单剩余商品和原订单信息
            $old_goods = I('old_goods');
            foreach ($orderGoods as $val) {
                if (empty($old_goods[$val['rec_id']])) {
                    M(OrderService::ORDER_GOODS_TABLE_NAME)->where("rec_id=" . $val['rec_id'])->delete();//删除商品
                } else {
                    //修改商品数量
                    if ($old_goods[$val['rec_id']] != $val['goods_num']) {
                        $val['goods_num'] = $old_goods[$val['rec_id']];
                        M(OrderService::ORDER_GOODS_TABLE_NAME)->where("rec_id=" . $val['rec_id'])->save(array('goods_num' => $val['goods_num']));
                    }
                    $oldArr[] = $val;//剩余商品
                }
                $all_goods[$val['rec_id']] = $val;//所有商品信息
            }
            $result = calculate_price($order['user_id'], $oldArr, $order['shipping_code'], 0, $order['province'],
                $order['city'], $order['district'], 0, 0, 0, 0);
            if ($result['status'] < 0) {
                $this->error($result['msg']);
            }
            //修改订单费用
            $res['goods_price'] = $result['result']['goods_price']; // 商品总价
            $res['order_amount'] = $result['result']['order_amount']; // 应付金额
            $res['total_amount'] = $result['result']['total_amount']; // 订单总价
            M(OrderService::TABLE_NAME)->where("order_id=" . $order_id)->save($res);
            //################################原单处理结束

            //################################新单处理
            for ($i = 1; $i < 20; $i++) {
                if (!empty($_POST[$i . '_old_goods'])) {
                    $split_goods[] = $_POST[$i . '_old_goods'];
                }
            }

            foreach ($split_goods as $key => $vrr) {
                foreach ($vrr as $k => $v) {
                    $all_goods[$k]['goods_num'] = $v;
                    $brr[$key][] = $all_goods[$k];
                }
            }

            foreach ($brr as $goods) {
                $result = calculate_price($order['user_id'], $goods, $order['shipping_code'], 0, $order['province'],
                    $order['city'], $order['district'], 0, 0, 0, 0);
                if ($result['status'] < 0) {
                    $this->error($result['msg']);
                }
                $new_order = $order;
                $new_order['order_sn'] = date('YmdHis') . mt_rand(1000, 9999);
                $new_order['parent_sn'] = $order['order_sn'];
                //修改订单费用
                $new_order['goods_price'] = $result['result']['goods_price']; // 商品总价
                $new_order['order_amount'] = $result['result']['order_amount']; // 应付金额
                $new_order['total_amount'] = $result['result']['total_amount']; // 订单总价
                $new_order['add_time'] = time();
                unset($new_order['order_id']);
                $new_order_id = M(OrderService::TABLE_NAME)->add($new_order);//插入订单表
                foreach ($goods as $vv) {
                    $vv['order_id'] = $new_order_id;
                    unset($vv['rec_id']);
                    $nid = M(OrderService::ORDER_GOODS_TABLE_NAME)->add($vv);//插入订单商品表
                }
            }
            //################################新单处理结束
            $this->success('操作成功', U('Shop/Order/detail', array('order_id' => $order_id)));
            exit;
        }

        foreach ($orderGoods as $val) {
            $brr[$val['rec_id']] = array(
                'goods_num' => $val['goods_num'],
                'goods_name' => getSubstr($val['goods_name'], 0, 35) . $val['spec_key_name']
            );
        }
        $this->assign('order', $order);
        $this->assign('goods_num_arr', json_encode($brr));
        $this->assign('orderGoods', $orderGoods);
        $this->display();
    }

    /*
     * 价钱修改
     */
    public function editprice($order_id) {
        $orderService = new OrderService();
        $order = $orderService->getOrderInfo($order_id);
        $this->editable($order);
        if (IS_POST) {
            $update['discount'] = I('post.discount');
            $update['shipping_price'] = I('post.shipping_price');
            $update['order_amount'] = $order['goods_price'] + $update['shipping_price'] - $update['discount'] - $order['user_money'] - $order['integral_money'] - $order['coupon_price'];
            $row = M(OrderService::TABLE_NAME)->where(array('order_id' => $order_id))->save($update);
            if (!$row) {
                $this->success('没有更新数据', U('Order/editprice', array('order_id' => $order_id)));
            } else {
                $this->success('操作成功', U('Order/detail', array('order_id' => $order_id)));
            }
            exit;
        }
        $this->assign('order', $order);
        $this->display();
    }

    /**
     * 删除订单接口
     *
     * @param $order_id
     */
    public function delete_order($order_id) {
        $orderService = new OrderService();
        $del = $orderService->delOrder($order_id);
        if ($del) {
            $this->success('删除订单成功', '', true);
        } else {
            $this->error('订单删除失败', '', true);
        }
    }

    /**
     * 订单取消付款
     */
    public function pay_cancel($order_id) {
        if (I('remark')) {
            $data = I('post.');
            $note = array('退款到用户余额', '已通过其他方式退款', '不处理，误操作项');
            if ($data['refundType'] == 0 && $data['amount'] > 0) {
                accountLog($data['user_id'], $data['amount'], 0, '退款到用户余额');
            }
            $orderService = new OrderService();
            $orderService->orderProcessHandle($data['order_id'], 'pay_cancel');
            $d = $orderService->orderActionLog($data['order_id'], 'pay_cancel',
                $data['remark'] . ':' . $note[$data['refundType']]);
            if ($d) {
                exit("<script>window.parent.pay_callback(1);</script>");
            } else {
                exit("<script>window.parent.pay_callback(0);</script>");
            }
        } else {
            $order = M(OrderService::TABLE_NAME)->where("order_id=$order_id")->find();
            $this->assign('order', $order);
            $this->display();
        }
    }

    /**
     * 订单打印
     *
     * @param int $id 订单id
     */
    public function order_print() {
        $order_id = I('order_id');
        $orderService = new OrderService();
        $order = $orderService->getOrderInfo($order_id);
        $order['province'] = getRegionName($order['province'], 1);
        $order['city'] = getRegionName($order['city'], 2);
        $order['district'] = getRegionName($order['district'], 3);
        $order['full_address'] = $order['province'] . ' ' . $order['city'] . ' ' . $order['district'] . ' ' . $order['address'];
        $orderGoods = $orderService->getOrderGoods($order_id);
        $shop = tpCache('shop_info');
        $this->assign('order', $order);
        $this->assign('shop', $shop);
        $this->assign('orderGoods', $orderGoods);
        $template = I('template', 'print');
        $this->display($template);
    }

    /**
     * 快递单打印
     */
    public function shipping_print() {
        $order_id = I('get.order_id');
        $orderService = new OrderService();
        $order = $orderService->getOrderInfo($order_id);
        //查询是否存在订单及物流
        $shipping = M('plugin')->where(array('code' => $order['shipping_code'], 'type' => 'shipping'))->find();
        if (!$shipping) {
            $this->error('物流插件不存在');
        }
        if (empty($shipping['config_value'])) {
            $this->error('请设置' . $shipping['name'] . '打印模板');
        }
        $shop = tpCache('shop_info');//获取网站信息
        $shop['province'] = empty($shop['province']) ? '' : getRegionName($shop['province'], 1);
        $shop['city'] = empty($shop['city']) ? '' : getRegionName($shop['city'], 2);
        $shop['district'] = empty($shop['district']) ? '' : getRegionName($shop['district'], 3);

        $order['province'] = getRegionName($order['province'], 1);
        $order['city'] = getRegionName($order['city'], 2);
        $order['district'] = getRegionName($order['district'], 3);
        if (empty($shipping['config'])) {
            $config = array('width' => 840, 'height' => 480, 'offset_x' => 0, 'offset_y' => 0);
            $this->assign('config', $config);
        } else {
            $this->assign('config', unserialize($shipping['config']));
        }
        $template_var = array(
            "发货点-名称",
            "发货点-联系人",
            "发货点-电话",
            "发货点-省份",
            "发货点-城市",
            "发货点-区县",
            "发货点-手机",
            "发货点-详细地址",
            "收件人-姓名",
            "收件人-手机",
            "收件人-电话",
            "收件人-省份",
            "收件人-城市",
            "收件人-区县",
            "收件人-邮编",
            "收件人-详细地址",
            "时间-年",
            "时间-月",
            "时间-日",
            "时间-当前日期",
            "订单-订单号",
            "订单-备注",
            "订单-配送费用"
        );
        $content_var = array(
            $shop['store_name'],
            $shop['contact'],
            $shop['phone'],
            $shop['province'],
            $shop['city'],
            $shop['district'],
            $shop['phone'],
            $shop['address'],
            $order['consignee'],
            $order['mobile'],
            $order['phone'],
            $order['province'],
            $order['city'],
            $order['district'],
            $order['zipcode'],
            $order['address'],
            date('Y'),
            date('M'),
            date('d'),
            date('Y-m-d'),
            $order['order_sn'],
            $order['admin_note'],
            $order['shipping_price'],
        );
        $shipping['config_value'] = str_replace($template_var, $content_var, $shipping['config_value']);
        $this->assign('shipping', $shipping);
        $this->display("Plugin/print_express");
    }

    /**
     * 生成发货单
     */
    public function deliveryHandle() {
        $orderService = new OrderService();
        $data = I('post.');
        $res = $orderService->deliveryHandle($data);
        if ($res) {
            $this->ajaxReturn(['msg'=>'操作成功', 'status'=>true, 'icon'=>1]);
        } else {
            $this->ajaxReturn(['msg'=>'操作失败', 'status'=>false, 'icon'=>2]);
        }
    }

    /**
     * 获取发货信息
     */
    public function delivery_info() {
        $order_id = I('order_id');
        if (IS_POST) {
            $orderService = new OrderService();
            $order = $orderService->getOrderInfo($order_id);
            $orderGoods = $orderService->getOrderGoods($order_id);
            $delivery_record = M(DeliveryService::TABLE_NAME)->where('order_id=' . $order_id)->select();
            if ($delivery_record) {
                $order['invoice_no'] = $delivery_record[count($delivery_record) - 1]['invoice_no'];
            }

            $this->ajaxReturn(['order'=> $order, 'orderGoods'=> $orderGoods, 'delivery_record'=> $delivery_record]);
        }
        $this->assign('order_id',$order_id);
        $this->display();
    }

    /**
     * 发货单列表
     */
    public function delivery_list() {
        $this->display();
    }

    /**
     * ajax 退货订单列表
     */
    public function ajax_return_list() {
        // 搜索条件
        $order_sn = trim(I('order_sn'));
        $order_by = I('order_by') ? I('order_by') : 'id';
        $sort_order = I('sort_order') ? I('sort_order') : 'desc';
        $status = I('status');

        $where = " 1 = 1 ";
        $order_sn && $where .= " and order_sn like '%$order_sn%' ";
        empty($order_sn) && $where .= " and status = '$status' ";
        $count = M(OrderService::RETURN_TABLE_NAME)->where($where)->count();
        $Page = new AjaxPage($count, 13);
        $show = $Page->show();
        $list = M(OrderService::RETURN_TABLE_NAME)->where($where)->order("$order_by $sort_order")->limit("{$Page->firstRow},{$Page->listRows}")->select();
        $goods_id_arr = get_arr_column($list, 'goods_id');
        if (!empty($goods_id_arr)) {
            $goods_list = M(GoodsService::GOODS_TABLE_NAME)->where("goods_id in (" . implode(',',
                    $goods_id_arr) . ")")->getField('goods_id,goods_name');
        }
        $this->assign('goods_list', $goods_list);
        $this->assign('list', $list);
        $this->assign('page', $show);// 赋值分页输出
        $this->display();
    }

    /**
     * 删除某个退换货申请
     */
    public function return_del() {
        $id = I('get.id');
        M(OrderService::RETURN_TABLE_NAME)->where("id = $id")->delete();
        $this->success('成功删除!');
    }

    /**
     * 退换货操作
     */
    public function return_info() {
        $id = I('id');
        $return_goods = M(OrderService::RETURN_TABLE_NAME)->where("id= $id")->find();
        if ($return_goods['imgs']) {
            $return_goods['imgs'] = explode(',', $return_goods['imgs']);
        }
        $user = M('ShopUsers')->where("userid = {$return_goods[user_id]}")->find();
        $goods = M(GoodsService::GOODS_TABLE_NAME)->where("goods_id = {$return_goods[goods_id]}")->find();
        $type_msg = array('退换', '换货');
        $status_msg = array('未处理', '处理中', '已完成');
        if (IS_POST) {
            $data['type'] = I('type');
            $data['status'] = I('status');
            $data['remark'] = I('remark');
            $note = "退换货:{$type_msg[$data['type']]}, 状态:{$status_msg[$data['status']]},处理备注：{$data['remark']}";
            $result = M(OrderService::RETURN_TABLE_NAME)->where("id= $id")->save($data);
            if ($result) {
                $type = empty($data['type']) ? 2 : 3;
                $where = " order_id = " . $return_goods['order_id'] . " and goods_id=" . $return_goods['goods_id'];
                M(OrderService::ORDER_GOODS_TABLE_NAME)->where($where)->save(array('is_send' => $type));//更改商品状态
                $orderService = new OrderService();
                $log = $orderService->orderActionLog($return_goods[order_id], 'refund', $note);
                $this->success('修改成功!');
                exit;
            }
        }

        $this->assign('id', $id); // 用户
        $this->assign('user', $user); // 用户
        $this->assign('goods', $goods);// 商品
        $this->assign('return_goods', $return_goods);// 退换货               
        $this->display();
    }

    /**
     * 管理员生成申请退货单
     */
    public function add_return_goods() {
        $order_id = I('order_id');
        $goods_id = I('goods_id');

        $return_goods = M(OrderService::RETURN_TABLE_NAME)->where("order_id = $order_id and goods_id = $goods_id")->find();
        if (!empty($return_goods)) {
            $this->error('已经提交过退货申请!', U('Shop/Order/return_list'));
            exit;
        }
        $order = M(OrderService::TABLE_NAME)->where("order_id = $order_id")->find();

        $data['order_id'] = $order_id;
        $data['order_sn'] = $order['order_sn'];
        $data['goods_id'] = $goods_id;
        $data['addtime'] = time();
        $data['user_id'] = $order['user_id'];
        $data['remark'] = '管理员申请退换货'; // 问题描述            
        M(OrderService::RETURN_TABLE_NAME)->add($data);
        $this->success('申请成功,现在去处理退货', U('Shop/Order/return_list'));
        exit;
    }

    /**
     * 订单操作
     */
    public function order_action() {
        $orderService = new OrderService();
        $action = I('get.type');
        $order_id = I('get.order_id');
        $order = M(OrderService::TABLE_NAME)->find($order_id);
        $note = I('post.note');
        if ($action && $order) {
            $res = $orderService->orderProcessHandle($order_id, $action, $note);
            if ($res) {
                exit(json_encode(array('status' => 1, 'msg' => '操作成功')));
            } else {
                exit(json_encode(array('status' => 0, 'msg' => '操作失败')));
            }
        } else {
            $this->error('参数错误', U('Shop/Order/detail', array('order_id' => $order_id)));
        }
    }

    /**
     * 订单日志
     */
    public function order_log() {
        $timegap = I('timegap');
        $begin = $end = null;
        if ($timegap) {
            $gap = explode('-', $timegap);
            $begin = strtotime($gap[0]);
            $end = strtotime($gap[1]);
        }
        $condition = array();
        $log = M(OrderService::ORDER_ACTION_TABLE_NAME);
        if ($begin && $end) {
            $condition['log_time'] = array('between', "$begin,$end");
        }
        // $admin_id = I('admin_id');
        // if($admin_id >0 ){
        // 	$condition['action_user'] = $admin_id;
        // }
        $count = $log->where($condition)->count();
        $Page = new \Shop\Util\Page($count, 20);
        foreach ($condition as $key => $val) {
            $Page->parameter[$key] = urlencode($val);
        }
        $show = $Page->show();
        $list = $log->where($condition)->order('action_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        // $admin = M('admin')->getField('admin_id,user_name');
//        $this->assign('admin', $admin);
        $this->display();
    }

    /**
     * 检测订单是否可以编辑
     *
     * @param $order
     */
    private function editable($order) {
        if ($order['shipping_status'] != 0) {
            $this->error('已发货订单不允许编辑');
            exit;
        }

        return;
    }

    public function export_order() {
        //搜索条件
        $where = 'where 1=1 ';
        $consignee = I('consignee');
        if ($consignee) {
            $where .= " AND consignee like '%$consignee%' ";
        }
        $order_sn = I('order_sn');
        if ($order_sn) {
            $where .= " AND order_sn = '$order_sn' ";
        }
        if (I('order_status')) {
            $where .= " AND order_status = " . I('order_status');
        }

        $timegap = I('timegap');
        if ($timegap) {
            $gap = explode('-', $timegap);
            $begin = strtotime($gap[0]);
            $end = strtotime($gap[1]);
            $where .= " AND add_time>$begin and add_time<$end ";
        }

        $sql = "select *,FROM_UNIXTIME(add_time,'%Y-%m-%d') as create_time from __PREFIX__shop_order $where order by order_id";
        $orderList = D()->query($sql);
        $strTable = '<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">订单编号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">日期</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收货人</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收货地址</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">电话</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">订单金额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">实际支付</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付方式</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付状态</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">发货状态</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品信息</td>';
        $strTable .= '</tr>';
        if (is_array($orderList)) {
            foreach ($orderList as $k => $val) {
                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;' . $val['order_sn'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['create_time'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['consignee'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . getRegionName($val['province'],
                        1) . "," . getRegionName($val['city'], 2) . "," . getRegionName($val['district'],
                        3) . ",{$val['address']}" . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['mobile'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['goods_price'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['order_amount'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['pay_name'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $this->pay_status[$val['pay_status']] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $this->shipping_status[$val['shipping_status']] . '</td>';
                $orderGoods = M(OrderService::ORDER_GOODS_TABLE_NAME)->where('order_id=' . $val['order_id'])->select();
                $strGoods = "";
                foreach ($orderGoods as $goods) {
                    $strGoods .= "商品编号：" . $goods['goods_sn'] . " 商品名称：" . $goods['goods_name'];
                    if ($goods['spec_key_name'] != '') {
                        $strGoods .= " 规格：" . $goods['spec_key_name'];
                    }
                    $strGoods .= "<br />";
                }
                unset($orderGoods);
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $strGoods . ' </td>';
                $strTable .= '</tr>';
            }
        }
        $strTable .= '</table>';
        unset($orderList);
        downloadExcel($strTable, 'order');
        exit();
    }

    /**
     * 退货单列表
     */
    public function return_list() {
        $this->display();
    }

    /**
     * 添加一笔订单
     */
    public function add_order() {
        $order = array();
        //  获取省份
        $province = M('AreaProvince')->where(array('parent_id' => 0, 'level' => 1))->select();
        //  获取订单城市
        $city = M('AreaCity')->where(array('parent_id' => $order['province'], 'level' => 2))->select();
        //  获取订单地区
        $area = M('AreaDistrict')->where(array('parent_id' => $order['city'], 'level' => 3))->select();
        //  获取支付方式
        $payment_list = OrderService::PAY_WAY();
        if (IS_POST) {
            $order['user_id'] = I('user_id');// 用户id 可以为空
            $order['consignee'] = I('consignee');// 收货人
            $order['province'] = I('province'); // 省份
            $order['city'] = I('city'); // 城市
            $order['district'] = I('district'); // 县
            $order['address'] = I('address'); // 收货地址
            $order['mobile'] = I('mobile'); // 手机           
            $order['invoice_title'] = I('invoice_title');// 发票
            $order['admin_note'] = I('admin_note'); // 管理员备注            
            $order['order_sn'] = date('YmdHis') . mt_rand(1000, 9999); // 订单编号;
            $order['add_time'] = time(); //添加时间
            $order['pay_code'] = I('payment');// 支付方式

            $goods_id_arr = I("goods_id");
            $orderService = new OrderService();
            $order_goods = $orderService->get_spec_goods($goods_id_arr);
            $result = calculate_price($order['user_id'], $order_goods, $order['shipping_code'], 0, $order['province'],
                $order['city'], $order['district'], 0, 0, 0, 0);
            if ($result['status'] < 0) {
                $this->error($result['msg']);
            }

            $order['goods_price'] = $result['result']['goods_price']; // 商品总价
            $order['shipping_price'] = $result['result']['shipping_price']; //物流费
            $order['order_amount'] = $result['result']['order_amount']; // 应付金额
            $order['total_amount'] = $result['result']['total_amount']; // 订单总价

            // 添加订单
            $order_id = M(OrderService::TABLE_NAME)->add($order);
            if ($order_id) {
                foreach ($order_goods as $key => $val) {
                    $val['order_id'] = $order_id;
                    $rec_id = M(OrderService::ORDER_GOODS_TABLE_NAME)->add($val);
                    if (!$rec_id) {
                        $this->error('添加失败');
                        return;
                    }
                }
                $this->success('添加订单成功', U("Shop/Order/detail", array('order_id' => $order_id)));
                return;
            } else {
                $this->error('添加失败');
                return;
            }
        }
        $this->assign('payment_list', $payment_list);
        $this->assign('province', $province);
        $this->assign('city', $city);
        $this->assign('area', $area);
        $this->display();
    }

    /**
     * 选择搜索商品
     */
    public function search_goods() {
        $brandList = M(BrandService::TABLIE_NAME)->select();
        $categoryList = M(CategoryService::TABLE_NAME)->select();
        $this->assign('categoryList', $categoryList);
        $this->assign('brandList', $brandList);
        $where = ' is_on_sale = 1 ';//搜索条件
        I('intro') && $where = "$where and " . I('intro') . " = 1";
        if (I('cat_id')) {
            $this->assign('cat_id', I('cat_id'));
            $grandson_ids = getCatGrandson(I('cat_id'));
            $where = " $where  and cat_id in(" . implode(',', $grandson_ids) . ") "; // 初始化搜索条件

        }
        if (I('brand_id')) {
            $this->assign('brand_id', I('brand_id'));
            $where = "$where and brand_id = " . I('brand_id');
        }
        if (!empty($_REQUEST['keywords'])) {
            $this->assign('keywords', I('keywords'));
            $where = "$where and (goods_name like '%" . I('keywords') . "%' or keywords like '%" . I('keywords') . "%')";
        }
        $goodsList = M(GoodsService::GOODS_TABLE_NAME)->where($where)->order('goods_id DESC')->limit(10)->select();

        foreach ($goodsList as $key => $val) {
            $spec_goods = M('shop_spec_goods_price')->where("goods_id = {$val['goods_id']}")->select();
            $goodsList[$key]['spec_goods'] = $spec_goods;
        }
        $this->assign('goodsList', $goodsList);
        $this->display();
    }

    public function ajaxOrderNotice() {
        $order_amount = M(OrderService::TABLE_NAME)->where("order_status=0 and (pay_status=1 or pay_code='cod')")->count();
        echo $order_amount;
    }
}
