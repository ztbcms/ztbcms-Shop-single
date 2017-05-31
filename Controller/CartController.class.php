<?php
namespace Shop\Controller;

use Shop\Service\CartService;

class CartController extends BaseController {

    /**
     * 购物车列表
     */
    public function index() {
        if(IS_GET){

            if (I('get.ids')) {
                $ids = I('get.ids');
            }else{
                $ids = 0;
            }

            $res = CartService::get_cart_list($this->userid, $ids);

            $this->ajaxReturn($res);

        }else{
            $this->ajaxReturn(array('status'=>false, 'data'=>null, 'msg'=>'请求方法错误'));
        }

        //如果用户没有登录，是用session_id加入购物车
//        $where = ['user_id' => $this->userid];
//        if (I('get.ids')) {
//            $where['id'] = ['in', I('get.ids')];
//        }
//        $cart_list = M(CartService::TABLE_NAME)->where($where)->order('id DESC')->select();
//        $this->success($cart_list ? $cart_list : [], '', true);

    }

    /**
     * 设置购物车数量
     *
     * @param cart_id int 购物车id
     */
    public function set_num() {

        if(IS_POST){
            $cart_id = I('post.cart_id');
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

//            $cart_service = new CartService();
//            //设置购物车数据操作
//            $result = $cart_service->add_cart($goods_id, $goods_num, $goods_spec, $this->session_id,$this->userid); // 将商品加入购物车
//            if ($result) {
//                $this->success($result, '', true);
//            } else {
//                $this->error($cart_service->get_err_msg(), '', true);
//            }
                $result = CartService::add_cart($goods_id, $goods_num, $goods_spec, $this->session_id,$this->userid); // 将商品加入购物车
                $this->ajaxReturn($result);


            } else {
                //如果购物车没有，默认是添加
                $goods_id = I("post.goods_id"); // 商品id
                $goods_num = I("post.goods_num", 1);// 商品数量
                $goods_spec = I("post.goods_spec"); // 商品规格

                if($goods_id == 0){
                    $this->ajaxReturn(array('status'=>false, 'data'=>null, 'msg'=>'商品不存在'));
                }

                if($goods_spec == 0){
                    $this->ajaxReturn(array('status'=>false, 'data'=>null, 'msg'=>'缺少商品规格'));
                }

                $result = CartService::add_cart($goods_id, $goods_num, $goods_spec, $this->session_id,$this->userid); // 将商品加入购物车
                $this->ajaxReturn($result);
            }

        }else{
            $this->ajaxReturn(array('status'=>false, 'data'=>null, 'msg'=>'请求方法错误'));

        }

    }

    /**
     * ajax 将商品加入购物车
     */
    function add_cart() {
        if(IS_POST){
            $goods_id = I("post.goods_id",0); // 商品id
            $goods_num = I("post.goods_num", 1);// 商品数量
            $goods_spec = I("post.goods_spec",0); // 商品规格

            if($goods_id == 0){
                $this->ajaxReturn(array('status'=>false, 'data'=>null, 'msg'=>'商品不存在'));
            }

            if($goods_spec == 0){
                $this->ajaxReturn(array('status'=>false, 'data'=>null, 'msg'=>'缺少商品规格'));
            }

            $result = CartService::add_cart($goods_id, $goods_num, $goods_spec, $this->session_id,$this->userid); // 将商品加入购物车
            $this->ajaxReturn($result);


        }else{
            $this->ajaxReturn(array('status'=>false, 'data'=>null, 'msg'=>'请求方法错误'));
        }


    }

    /**
     * 移除购物车
     */
    function del_cart() {
        if(IS_POST){
            $id = I('post.cart_id',0);  //购物车id

            if($id == 0){
                $this->ajaxReturn(array('status'=>false, 'data'=>null, 'msg'=>'缺少购物车id'));
            }

            $res = CartService::del_cart($id,$this->userid);
            $this->ajaxReturn($res);
        }else{
            $this->ajaxReturn(array('status'=>false, 'data'=>null, 'msg'=>'请求方法错误'));
        }
    }

    /**
     * 通过订单编号再来一单，返回购物车id
     */
    public function order_again() {
        if(IS_POST){
            $order_id = I('post.order_id');
            $goods_list = M('OrderGoods')->where(['order_id' => $order_id])->select();
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

//            $cart_service = new CartService();
//            //设置购物车数据操作
//            $result = $cart_service->add_cart($goods_id, $goods_num, $goods_spec, $this->session_id,
//                $this->userid); // 将商品加入购物车

                $res = CartService::add_cart($goods_id, $goods_num, $goods_spec, $this->session_id,$this->userid);

                if($res['status']){
                    $result_arr[] = $res['data'];
                }else{
                    $this->ajaxReturn($res);
                }
//            $result_arr[] = $result;
            }

            if ($res['status']) {
                $this->ajaxReturn(array('status'=>true, 'data'=>$result_arr, 'msg'=>'添加成功!'));
            } else {
                $this->ajaxReturn($res);
            }
        }else{
            $this->ajaxReturn(array('status'=>false, 'data'=>null, 'msg'=>'请求方法错误'));
        }

    }
}