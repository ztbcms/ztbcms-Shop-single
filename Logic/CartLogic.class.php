<?php

namespace Shop\Logic;

use Common\Model\RelationModel;
use Shop\Service\CartService;
use Shop\Service\GoodsService;

/**
 * 购物车 逻辑定义
 * Class CatsLogic
 * @package Home\Logic
 */
class CartLogic extends RelationModel
{

    /**
     * 购物车列表 
     * @param array $user   用户
     * @param string $session_id  session_id
     * @param int $selected  是否被用户勾选中的 0 为全部 1为选中  一般没有查询不选中的商品情况
     * @param int $mode 0  返回数组形式  1 直接返回result
     * @return array
     */
    function cartList($user = array() , $session_id = '', $selected = 0,$mode =0)
    {                   
        
        $where = " 1 = 1 ";
        //if($selected != NULL)
        //    $where = " selected = $selected "; // 购物车选中状态
        
        if($user['user_id'])// 如果用户已经登录则按照用户id查询
        {
             $where .= " and user_id = $user[user_id] ";
             // 给用户计算会员价 登录前后不一样             
        }           
        else
        {
            $where .= " and session_id = '$session_id'";
            $user['user_id'] = 0;
        }
                                
        $cartList = M(CartService::TABLE_NAME)->where($where)->select();  // 获取购物车商品
        $anum = $total_price =  $cut_fee = 0;

        foreach ($cartList as $k=>$val){
        	$cartList[$k]['goods_fee'] = $val['goods_num'] * $val['member_goods_price'];
        	$cartList[$k]['store_count']  = getGoodNum($val['goods_id'],$val['spec_key']); // 最多可购买的库存数量        	
                $anum += $val['goods_num'];
                
                // 如果要求只计算购物车选中商品的价格 和数量  并且  当前商品没选择 则跳过
                if($selected == 1 && $val['selected'] == 0)
                    continue;
                
                $cut_fee += $val['goods_num'] * $val['market_price'] - $val['goods_num'] * $val['member_goods_price'];                
        	$total_price += $val['goods_num'] * $val['member_goods_price'];
        }

        $total_price = array('total_fee' =>$total_price , 'cut_fee' => $cut_fee,'num'=> $anum,); // 总计        
        setcookie('cn',$anum,null,'/');
        if($mode == 1) return array('cartList' => $cartList, 'total_price' => $total_price);
        return array('status'=>1,'msg'=>'','result'=>array('cartList' =>$cartList, 'total_price' => $total_price));
    }    
    
/**
 * 计算商品的的运费 
 * @param string $shipping_code 物流 编号
 * @param string $province  省份
 * @param string $city     市
 * @param string $district  区
 * @return int
 */
function cart_freight2($shipping_code,$province,$city,$district,$weight)
{
    
    if($weight == 0) return 0; // 商品没有重量
    if($shipping_code == '') return 0;               
  
   // 先根据 镇 县 区找 shipping_area_id   
      $shipping_area_id = M('AreaRegion')->where("shipping_area_id in (select shipping_area_id from  ".C('DB_PREFIX')."shipping_area where shipping_code = '$shipping_code') and region_id = {$district}")->getField('shipping_area_id');
    
    // 先根据市区找 shipping_area_id
   if($shipping_area_id == false)    
      $shipping_area_id = M('AreaRegion')->where("shipping_area_id in (select shipping_area_id from  ".C('DB_PREFIX')."shipping_area where shipping_code = '$shipping_code') and region_id = {$city}")->getField('shipping_area_id');

   // 市区找不到 根据省份找shipping_area_id
   if($shipping_area_id == false)
        $shipping_area_id = M('AreaRegion')->where("shipping_area_id in (select shipping_area_id from  ".C('DB_PREFIX')."shipping_area where shipping_code = '$shipping_code') and region_id = {$province}")->getField('shipping_area_id');

   // 省份找不到 找默认配置全国的物流费
   if($shipping_area_id == false)
   {           
        // 如果市和省份都没查到, 就查询 tp_shipping_area 表 is_default = 1 的  表示全国的  select * from `tp_plugin`  select * from  `tp_shipping_area` select * from  `tp_area_region`           
       $shipping_area_id = M("ShippingArea")->where("shipping_code = '$shipping_code' and is_default = 1")->getField('shipping_area_id');
   }
   if($shipping_area_id == false)
       return 0;
   /// 找到了 shipping_area_id  找config       
   $shipping_config = M('ShippingArea')->where("shipping_area_id = $shipping_area_id")->getField('config');
   $shipping_config  = unserialize($shipping_config);
   $shipping_config['money'] = $shipping_config['money'] ? $shipping_config['money'] : 0;

   // 1000 克以内的 只算个首重费
   if($weight < $shipping_config['first_weight'])
   {          
       return $shipping_config['money'];     
   }
   // 超过 1000 克的计算方法 
   $weight = $weight - $shipping_config['first_weight']; // 续重
   $weight = ceil($weight / $shipping_config['second_weight']); // 续重不够取整 
   $freight = $shipping_config['money'] +  $weight * $shipping_config['add_money']; // 首重 + 续重 * 续重费       
   
   return $freight;  
}
  
    /**
     * 获取用户可以使用的优惠券
     * @param string $user_id  用户id
     * @param string $coupon_id 优惠券id
     * @param  int $mode 0  返回数组形式  1 直接返回result
     * @return array|int
     */
//    public function getCouponMoney($user_id, $coupon_id,$mode)
//    {
//        $couponlist = M('CouponList')->where("uid = $user_id and id = $coupon_id")->find(); // 获取用户的优惠券
//        if(empty($couponlist)) {
//            if($mode == 1) return 0;
//            return array('status'=>1,'msg'=>'','result'=>0);
//        }
//
//        $coupon = M('Coupon')->where("id = {$couponlist['cid']}")->find(); // 获取 优惠券类型表
//        $coupon['money'] = $coupon['money'] ? $coupon['money'] : 0;
//
//        if($mode == 1) return $coupon['money'];
//        return array('status'=>1,'msg'=>'','result'=>$coupon['money']);
//    }
    
    /**
     * 根据优惠券代码获取优惠券金额
     * @param string $couponCode 优惠券代码
     * @param string|float $order_momey Description 订单金额
     * @return array -1 优惠券不存在 -2 优惠券已过期 -3 订单金额没达到使用券条件
     */
//    public function getCouponMoneyByCode($couponCode,$order_momey)
//    {
//        $couponlist = M('CouponList')->where("code = '$couponCode'")->find(); // 获取用户的优惠券
//        if(empty($couponlist))
//            return array('status'=>-9,'msg'=>'优惠券码不存在','result'=>'');
//        $coupon = M('Coupon')->where("id = {$couponlist['cid']}")->find(); // 获取优惠券类型表
//        if(time() > $coupon['use_end_time'])
//            return array('status'=>-10,'msg'=>'优惠券已经过期','result'=>'');
//        if($order_momey < $coupon['condition'])
//            return array('status'=>-11,'msg'=>'金额没达到优惠券使用条件','result'=>'');
//        if($couponlist['order_id'] > 0)
//            return array('status'=>-12,'msg'=>'优惠券已被使用','result'=>'');
//
//        return array('status'=>1,'msg'=>'','result'=>$coupon['money']);
//    }
    
    /**
     *  添加一个订单
     * @param string $user_id  用户id
     * @param array $cartList  选中购物车商品
     * @param string $address_id 地址id
     * @param string $shipping_code 物流编号
     * @param string $invoice_title 发票
     * @param string|int $coupon_id 优惠券id
     * @param array $car_price 各种价格
     * @return string $order_id 返回新增的订单id
     */
    public function addOrder($user_id,$cartList,$address_id,$shipping_code,$invoice_title,$coupon_id = 0,$car_price){
        
        // 仿制灌水 1天只能下 50 单  // select * from `tp_order` where user_id = 1  and order_sn like '20151217%' 
        $order_count = M('Order')->where("user_id= $user_id and order_sn like '".date('Ymd')."%'")->count(); // 查找购物车商品总数量
        if($order_count >= 50) 
            return array('status'=>-9,'msg'=>'一天只能下50个订单','result'=>'');            
        
         // 0插入订单 order
        $address = M('UserAddress')->where("address_id = $address_id")->find();
        $shipping = M('Plugin')->where("code = '$shipping_code'")->find();
        $data = array(
                'order_sn'         => date('YmdHis').rand(1000,9999), // 订单编号
                'user_id'          =>$user_id, // 用户id
                'consignee'        =>$address['consignee'], // 收货人
                'province'         =>$address['province'],//'省份id',
                'city'             =>$address['city'],//'城市id',
                'district'         =>$address['district'],//'县',
                'twon'             =>$address['twon'],// '街道',
                'address'          =>$address['address'],//'详细地址',
                'mobile'           =>$address['mobile'],//'手机',
                'zipcode'          =>$address['zipcode'],//'邮编',            
                'email'            =>$address['email'],//'邮箱',
                'shipping_code'    =>$shipping_code,//'物流编号',
                'shipping_name'    =>$shipping['name'], //'物流名称',                       
                'invoice_title'    =>$invoice_title, //'发票抬头',                
                'goods_price'      =>$car_price['goodsFee'],//'商品价格',
                'shipping_price'   =>$car_price['postFee'],//'物流价格',                
                'user_money'       =>$car_price['balance'],//'使用余额',
                'coupon_price'     =>$car_price['couponFee'],//'使用优惠券',                        
                'integral'         =>($car_price['pointsFee'] * tpCache('shopping.point_rate')), //'使用积分',
                'integral_money'   =>$car_price['pointsFee'],//'使用积分抵多少钱',
                'total_amount'     =>($car_price['goodsFee'] + $car_price['postFee']),// 订单总额
                'order_amount'     =>$car_price['payables'],//'应付款金额',                
                'add_time'         =>time(), // 下单时间                
                'order_prom_id'    =>$car_price['order_prom_id'],//'订单优惠活动id',
                'order_prom_amount'=>$car_price['order_prom_amount'],//'订单优惠活动优惠了多少钱',
        );
        
        $order_id = M("Order")->data($data)->add();
        if(!$order_id)  
            return array('status'=>-8,'msg'=>'添加订单失败','result'=>NULL);
        
        // 记录订单操作日志
        logOrder($order_id,'您提交了订单，请等待系统确认','提交订单',$user_id);        
        $order = M('Order')->where("order_id = $order_id")->find();                
        // 1插入order_goods 表
        $order_goods_ids=array();
        foreach($cartList as $key => $val){
           $order_goods_ids[]=$val['goods_id'];
           $goods = M(GoodsService::GOODS_TABLE_NAME)->where("goods_id = {$val['goods_id']} ")->find();
           $data2['order_id']           = $order_id; // 订单id
           $data2['goods_id']           = $val['goods_id']; // 商品id
           $data2['goods_name']         = $val['goods_name']; // 商品名称
           $data2['goods_sn']           = $val['goods_sn']; // 商品货号
           $data2['goods_num']          = $val['goods_num']; // 购买数量
           $data2['market_price']       = $val['market_price']; // 市场价
           $data2['goods_price']        = $val['goods_price']; // 商品价
           $data2['spec_key']           = $val['spec_key']; // 商品规格
           $data2['spec_key_name']      = $val['spec_key_name']; // 商品规格名称
           $data2['sku']           		= $val['sku']; // 商品sku
           $data2['member_goods_price'] = $val['member_goods_price']; // 会员折扣价
           $data2['cost_price']         = $goods['cost_price']; // 成本价
           $data2['give_integral']      = $goods['give_integral']; // 购买商品赠送积分         
           $data2['prom_type']          = $val['prom_type']; // 0 普通订单,1 限时抢购, 2 团购 , 3 促销优惠
           $data2['prom_id']            = $val['prom_id']; // 活动id
           $order_goods_id              = M("OrderGoods")->data($data2)->add(); 
           // 扣除商品库存  扣除库存移到 付完款后扣除
           //M('ShopGoods')->where("goods_id = ".$val['goods_id'])->setDec('store_count',$val['goods_num']); // 商品减少库存
        } 
        // 如果应付金额为0  可能是余额支付 + 积分 + 优惠券 这里订单支付状态直接变成已支付 
        if($data['order_amount'] == 0)
        {                        
            update_pay_status($order['order_sn'], 1);    
        }           
        
        // 2修改优惠券状态  
        if($coupon_id > 0){
        	$data3['uid'] = $user_id;
        	$data3['order_id'] = $order_id;
        	$data3['use_time'] = time();
        	M('CouponList')->where("id = $coupon_id")->save($data3);
                $cid = M('CouponList')->where("id = $coupon_id")->getField('cid');
                M('Coupon')->where("id = $cid")->setInc('use_num'); // 优惠券的使用数量加一
        }
        // 3 扣除积分 扣除余额
        if($car_price['pointsFee']>0)
        	M('ShopUsers')->where("userid = $user_id")->setDec('pay_points',($car_price['pointsFee'] * tpCache('shopping.point_rate'))); // 消费积分 
        if($car_price['balance']>0)
        	M('ShopUsers')->where("userid = $user_id")->setDec('user_money',$car_price['balance']); // 抵扣余额         
        // 4 删除已提交订单商品

        $where=array('userid'=>$user_id,'goods_id'=>array('in',$order_goods_ids));
        M(CartService::TABLE_NAME)->where($where)->delete();
      
        // 5 记录log 日志
        $data4['user_id'] = $user_id;
        $data4['user_money'] = -$car_price['balance'];
        $data4['pay_points'] = -($car_price['pointsFee'] * tpCache('shopping.point_rate'));
        $data4['change_time'] = time();
        $data4['desc'] = '下单消费';
        $data4['order_sn'] = $order['order_sn'];
        $data4['order_id'] = $order_id;    
        // 如果使用了积分或者余额才记录
        ($data4['user_money'] || $data4['pay_points']) && M("AccountLog")->add($data4);           
        return array('status'=>1,'msg'=>'提交订单成功','result'=>$order_id); // 返回新增的订单id        
    }
    
    /**
     * 查看购物车的商品数量
     * @param string $user_id
     * @param $mode 0  返回数组形式  1 直接返回result
     * @return array
     */
    public function cart_count($user_id,$mode = 0){
        $count = M('ShopSpecItem')->where("user_id = $user_id and selected = 1")->count();
        if($mode == 1) return  $count;
        
        return array('status'=>1,'msg'=>'','result'=>$count);         
    }
        
   /**
    * 获取商品团购价
    * 如果商品没有团购活动 则返回 0
    * @param string $goods_id
    * @param $mode 0  返回数组形式  1 直接返回result
    * @return array|int
    */
   public function get_group_buy_price($goods_id,$mode=0)
   {
       $group_buy = M('GroupBuy')->where("goods_id = $goods_id and ".time()." >= start_time and ".time()." <= end_time ")->find(); // 找出这个商品                      
       if(empty($group_buy))       
            return 0;
       
        if($mode == 1) return $group_buy['groupbuy_price'];
        return array('status'=>1,'msg'=>'','result'=>$group_buy['groupbuy_price']);       
   }  
   
   /**
    * 用户登录后 需要对购物车 一些操作
    * @param string $session_id
    * @param string $user_id
    * @return bool
    */
   public function login_cart_handle($session_id,$user_id)
   {
	   if(empty($session_id) || empty($user_id))
	     return false;
        // 登录后将购物车的商品的 user_id 改为当前登录的id            
        M('ShopSpecItem')->where("session_id = '$session_id'")->save(array('user_id'=>$user_id));                    
        
        $Model = new \Think\Model();
        // 查找购物车两件完全相同的商品
        $cart_id_arr = $Model->query("select id from `__PREFIX__cart` where user_id = $user_id group by  goods_id,spec_key having count(goods_id) > 1");
        if(!empty($cart_id_arr))
        {
            $cart_id_arr = get_arr_column($cart_id_arr, 'id');
            $cart_id_str = implode(',', $cart_id_arr);
            M('ShopSpecItem')->delete($cart_id_str); // 删除购物车完全相同的商品
        }
   }
}