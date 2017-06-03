<?php
namespace Shop\Service;

class CartService extends BaseService {

    const TABLE_NAME = 'ShopCart';

    /**
     * 加入购物车
     *
     * @param int   $goods_id   商品ID
     * @param int   $goods_num  购买数量
     * @param array $goods_spec 商品规格
     * @param       $session_id
     * @param int   $user_id    用户ID
     * @return bool|int|array
     */
    static function addCart($goods_id, $goods_num, $goods_spec, $session_id = '', $user_id = 0) {
        $goods = M(GoodsService::GOODS_TABLE_NAME)->where("goods_id = '%d'", $goods_id)->find(); // 找出这个商品
        $specGoodsPriceList = M('ShopSpecGoodsPrice')->where("goods_id = '%d'",
            $goods_id)->getField("key,key_name,price,store_count,sku"); // 获取商品对应的规格价钱 库存 条码

        $user_id = $user_id ? $user_id : 0;

        foreach ($goods_spec as $key => $val) {
            $spec_item[] = $val;
        } // 所选择的规格项

        if (!empty($spec_item)) {
            // 有选择商品规格
            sort($spec_item);
            $spec_key = implode('_', $spec_item);
            if ($specGoodsPriceList[$spec_key]['store_count'] < $goods_num) {
                return self::createReturn(false, '', '商品库存不足');
            }
            $spec_price = $specGoodsPriceList[$spec_key]['price']; // 获取规格指定的价格
        }

        // 查询购物车是否已经存在这商品
        $where = [
            'goods_id' => $goods_id,
            'user_id' => $user_id
        ];

        if ($spec_key) {
            $where['spec_key'] = $spec_key;
        }

        $cart_goods = M(self::TABLE_NAME)->where($where)->find();  // 查找购物车是否已经存在该商品
        $price = $spec_price ? $spec_price : $goods['shop_price']; // 如果商品规格没有指定价格则用商品原始价格
        $catr_count = M(self::TABLE_NAME)->where($where)->count(); // 查找购物车商品总数量
        if ($catr_count >= 20) {
            return self::createReturn(false, '', '购物车最多只能放20种商品');
        }

        if (!empty($specGoodsPriceList) && empty($goods_spec)) {
            // 有商品规格 但是前台没有传递过来
            return self::createReturn(false, '', '必须传递商品规格');
        }
        if ($cart_goods['goods_num'] + $goods_num <= 0) {
            return self::createReturn(false, '', '购买商品数量不能为0');
        }
        if (empty($goods)) {
            return self::createReturn(false, '', '购买商品不存在');
        }
        if (($goods['store_count'] < ($cart_goods['goods_num'] + $goods_num))) {
            return self::createReturn(false, '', '商品库存不足');
        }

        $data = array(
            'user_id' => $user_id,   // 用户id
            'session_id' => $session_id,   // sessionid
            'goods_id' => $goods_id,   // 商品id
            'goods_sn' => $goods['goods_sn'],   // 商品货号
            'goods_name' => $goods['goods_name'],   // 商品名称
            'market_price' => $goods['market_price'],   // 市场价
            'goods_price' => $price,  // 购买价
            'member_goods_price' => $price,  // 会员折扣价 默认为 购买价
            'goods_num' => $goods_num, // 购买数量
            'spec_key' => "{$spec_key}", // 规格key
            'spec_key_name' => "{$specGoodsPriceList[$spec_key]['key_name']}", // 规格 key_name
            'sku' => "{$specGoodsPriceList[$spec_key]['sku']}", // 商品条形码
            'add_time' => time(), // 加入购物车时间
            'prom_type' => $goods['prom_type'],   // 0 普通订单,1 限时抢购, 2 团购 , 3 促销优惠
            'prom_id' => $goods['prom_id'],   // 活动id
            'original_img' => $goods['original_img']
        );

        // 如果商品购物车已经存在
        if ($cart_goods) {
            // 如果购物车的已有数量加上 这次要购买的数量  大于  库存  则不再增加数量
            if (($cart_goods['goods_num'] + $goods_num) > $goods['store_count']) {
                $goods_num = 0;
            }
            $update = array();
            $update['goods_num'] = ($cart_goods['goods_num'] + $goods_num);
            $update['add_time'] = time();
            $res = M(self::TABLE_NAME)->where("id ='%d'", $cart_goods['id'])->save($update); // 数量相加
            $cart_count = self::cartGoodsNum($user_id)['data']; // 查找购物车数量
            setcookie('cn', $cart_count, null, '/');
        } else {
            $res = M(self::TABLE_NAME)->add($data);
            $cart_count = self::cartGoodsNum($user_id); // 查找购物车数量
            setcookie('cn', $cart_count, null, '/');
        }
        if ($res) {
            //返回购物车id
            if ($cart_goods) {
                $cart_id = $cart_goods['id'];
            } else {
                $cart_id = $res;
            }

            return self::createReturn(true, $cart_id, '操作购物车成功');
        } else {
            return self::createReturn(false, '', '操作购物车失败');
        }
    }

    /**
     * 获取购物车列表
     *
     * @param int $userid 用户id
     * @param int $ids
     * @return array
     */
    static function getCartList($userid, $ids = 0) {
        //如果用户没有登录，是用session_id加入购物车
        $where = ['user_id' => $userid];
        if ($ids != 0) {
            $where['id'] = ['in', $ids];
        }
        $cart_list = M(CartService::TABLE_NAME)->where($where)->order('id DESC')->select();
        if ($cart_list) {
            return self::createReturn(true, $cart_list, '获取购物车成功!');
        } else {
            return self::createReturn(true, [], '购物车为空');
        }
    }


    /**
     * 移除购物车
     *
     * @param $cart_id int 购物车id
     * @param $userid  int 用户id
     * @return array
     */
    static function delCart($cart_id, $userid) {
        if ($cart_id == 0) {
            return self::createReturn(false, '', '参数错误');
        }
        $where['userid'] = $userid;
        $where['id'] = $cart_id;
        $res = M(CartService::TABLE_NAME)->where($where)->delete();
        if ($res) {
            return self::createReturn(true, $cart_id, '移除购物车成功!');
        } else {
            return self::createReturn(false, $res, '无可删除内容!');
        }
    }

    /**
     * 查看某个用户购物车中商品的数量
     *
     * @param string|int $user_id
     * @return int 购买数量
     */
    static function cartGoodsNum($user_id = 0) {
        $where['user_id'] = $user_id;
        // 查找购物车数量
        $cart_count = M('ShopCart')->where($where)->sum('goods_num');
        $cart_count = $cart_count ? $cart_count : 0;

        return self::createReturn(true, $cart_count, '');
    }

    /**
     * 更具订单id 添加已经选购商品
     *
     * @param $order_id
     * @param $userid
     * @return array|bool|int
     */
    static function orderAgain($order_id, $userid) {
        $goods_list = M(OrderService::ORDER_GOODS_TABLE_NAME)->where(['order_id' => $order_id])->select();
        $result_arr = [];
        foreach ($goods_list as $key => $value) {
            $goods_id = $value['goods_id'];
            $goods_num = $value['goods_num'];
            //将sku信息转化成数组
            if ($value['spec_key_name']) {
                $spec_key_name = explode(' ', $value['spec_key_name']);
                $spec_key = explode('_', $value['spec_key']);
                $spec_arr = null;
                foreach ($spec_key_name as $key => $value) {
                    $spec_arr[explode(':', $value)[0]] = $spec_key[$key];
                };
                $goods_spec = $spec_arr;
            }
            $res = CartService::addCart($goods_id, $goods_num, $goods_spec, '', $userid);
            if ($res['status']) {
                $result_arr[] = $res['data'];
            } else {
                return $res;
            }
        }

        if ($res['status']) {
            return self::createReturn(true, $result_arr, '添加成功');
        } else {
            return $res;
        }
    }

    static function setNum($cart_id = 0, $userid) {
        $cart = M(CartService::TABLE_NAME)->find($cart_id);
        if ($cart && $cart_id) {
            $set_num = (int)I('post.set_num');
            $goods_id = $cart['goods_id'];
            $goods_num = $set_num - $cart['goods_num'];
            //将sku信息转化成数组
            if ($cart['spec_key_name']) {
                $spec_key_name = explode(' ', $cart['spec_key_name']);
                $spec_key = explode('_', $cart['spec_key']);
                $spec_arr = null;
                foreach ($spec_key_name as $key => $value) {
                    $spec_arr[explode(':', $value)[0]] = $spec_key[$key];
                };
                $goods_spec = $spec_arr;
            }

            $result = self::addCart($goods_id, $goods_num, $goods_spec, '', $userid);

            return $result;
        } else {
            //如果购物车没有，默认是添加
            $goods_id = I("post.goods_id"); // 商品id
            $goods_num = I("post.goods_num", 1);// 商品数量
            $goods_spec = I("post.goods_spec"); // 商品规格
            $result = self::addCart($goods_id, $goods_num, $goods_spec, '', $userid); // 将商品加入购物车
            return $result;
        }
    }
}