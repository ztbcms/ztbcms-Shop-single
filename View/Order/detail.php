<include file="Public/min-header"/>
<div class="wrapper">
  <include file="Public/breadcrumb"/>
    <section class="content">
    <div class="row">
      <div class="col-xs-12">
      	<div class="box">
           <nav class="navbar navbar-default">	     
			<div class="collapse navbar-collapse">
                <div class="navbar-form pull-right margin">
                      <if condition="$order['order_status'] lt 2">
                         <a href="{:U('Order/edit_order',array('order_id'=>$order['order_id']))}" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="编辑">修改订单</a>
                      </if>
                      <if condition="($split eq 1) and ($order['order_status'] lt 2)">
                         <a href="{:U('Order/split_order',array('order_id'=>$order['order_id']))}" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="编辑">拆分订单</a>
                      </if>
                      <a href="{:U('Order/order_print',array('order_id'=>$order['order_id']))}" target="_blank" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="打印订单">
	                     <i class="fa fa-print"></i>打印订单
	                  </a>
                      <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
               </div>
            </div>
           </nav>
   
        <!--新订单列表 基本信息-->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-center">基本信息</h3>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <td>订单 ID</td>
                        <td>订单号</td>
                        <td>会员</td>
                        <td>E-Mail</td>
                        <td>电话</td>
                        <td>应付</td>
                        <td>订单状态</td>
                        <td>下单时间</td>
                        <td>支付时间</td>
                        <td>支付方式</td>
                    </tr>
                    <tr>
                        <td>{$order.order_id}</td>
                        <td>{$order.order_sn}</td>
                        <td><a href="#" target="_blank">{$order.consignee}</a></td>
                        <td><a href="#">{$order.email}</a></td>
                        <td>{$order.mobile}</td>
                        <td>{$order.order_amount}</td>
                        <td id="order-status">{$order_status[$order[order_status]]} / {$pay_status[$order[pay_status]]}<if condition="$order['pay_code'] eq 'cod'"><span style="color: red">(货到付款)</span></if> / {$shipping_status[$order[shipping_status]]}</td>
                    	<td>{$order.add_time|date='Y-m-d H:i',###}</td>
                    	<td><if condition="$order.pay_time neq 0">
                                {$order.pay_time|date='Y-m-d H:i',###}
                         <else/>
                            N
                         </if>
                        </td>             
                        <td id="pay-type">
                            {$order.pay_name|default='其他方式'}
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
        <!--新订单列表 收货人信息-->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-center">收货信息</h3>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tbody><tr>
					<td>收货人</td>
					<td>联系方式</td>
					<td>地址</td>
					<td>邮编</td>
					<td>配送方式</td>
			 
                    </tr>
                    <tr>
                        <td>{$order.consignee}</td>
                        <td>{$order.mobile}</td>
                        <td>{$order.address2}</td>
                        <td>
                            <if condition="$order.zipcode neq ''">
                                {$order.zipcode}
                                <else/>
                                N
                            </if>
                        </td>
                        <td>
                            {$order.shipping_name}
                            <if condition="$order[shipping_status] eq 1">
                                <a href="{:U('Shop/Order/shipping_print',array('order_id'=>$order['order_id'],'code'=>$order['shipping_code']))}" target="_blank" class="btn btn-primary input-sm" onclick="">打印快递单</a>
                            </if>
                        </td>                      
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--新订单列表 商品信息-->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-center">商品信息 </h3>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td class="text-left">商品</td>
                        <td class="text-left">属性</td>
                        <td class="text-right">数量</td>
                        <td class="text-right">单品价格</td>
                        <td class="text-right">会员折扣价</td>
                        <td class="text-right">单品小计</td>
                    </tr>
                    </thead>
                    <tbody>

                    <volist name="orderGoods" id="good">
                        <tr>
                            <td class="text-left"><a href="{:U('Shop/Goods/addEditGoods',array('id'=>$good['goods_id']))}" >{$good.goods_name}</a>
                            </td>
                            <td class="text-left">{$good.goods_attr}</td>
                            <td class="text-right">{$good.goods_num}</td>
                            <td class="text-right">{$good.goods_price}</td>
                            <td class="text-right">{$good.member_goods_price}</td>
                            <td class="text-right">{$good.goods_total}</td>
                        </tr>
                    </volist>

                    <tr>
                        <td colspan="4" class="text-right">小计:</td>
                        <td class="text-right">{$order.goods_price}</td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
        <!--新订单列表 费用信息-->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-center">费用信息
                    <a class="btn btn-primary btn-xs" data-original-title="修改费用" title="" data-toggle="tooltip" href="{:U('Shop/Order/editprice',array('order_id'=>$order['order_id']))}">
                    <i class="fa fa-pencil"></i>
                </a></h3>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <td class="text-right">小计</td>
                        <td class="text-right">运费</td>
                        <td class="text-right">积分 (-{$order.integral})</td>
                        <td class="text-right">余额抵扣</td>
                        <td class="text-right">优惠券抵扣</td>
                        <td class="text-right">价格调整</td>
                        <td class="text-right">应付</td>
                    </tr>
                    <tr>
                        <td class="text-right">{$order.goods_price}</td>
                        <td class="text-right">+{$order.shipping_price}</td>
                        <td class="text-right">-{$order.integral_money}</td>
                        <td class="text-right">-{$order.user_money}</td>
                        <td class="text-right">-{$order.coupon_price}</td>
                        <td class="text-right">减:{$order.discount}</td>
                        <td class="text-right">{$order.order_amount}</td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
        <!--新订单列表 操作信息-->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-center">操作信息</h3>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <div class="row">
                            <td class="text-right col-sm-2"><p class="margin">操作备注</p></td>
                            <td colspan="3">
                                <form id="order-action">
                                    <textarea name="note" placeholder="请输入操作备注" rows="3" class="form-control"></textarea>
                                </form>
                            </td>
                        </div>
                    </tr>
                    <tr>
                        <div class="row">
                            <td class="text-right col-sm-2"><p class="margin">当前可执行操作</p></td>
                            <td colspan="3">
                                <div class="input-group">
                                	<foreach name="button" item="vo" key="k">
                                		<if condition="$k eq 'pay_cancel'">
                                			<a class="btn btn-primary margin" href="javascript:void(0)" data-url="{:U('Order/pay_cancel',array('order_id'=>$order['order_id']))}" onclick="pay_cancel(this)">{$vo}</a>
                                		<elseif condition="$k eq 'delivery'"/>                                                 
                                			<a class="btn btn-primary margin" href="{:U('Order/delivery_info',array('order_id'=>$order['order_id']))}">{$vo}</a>
                                		<elseif condition="$k eq 'refund'"/>
                                			<!--退货商品列表-->
											<!-- <input class="btn btn-primary" type="button" onclick="selectGoods2({$order['order_id']})" value="退货申请"> 	-->
                                		<else/>
                                		<button class="btn btn-primary margin" onclick="ajax_submit_form('order-action','{:U('Shop/Order/order_action',array('order_id'=>$order['order_id'],'type'=>$k))}');" type="button" id="confirm">
                                		{$vo}</button>
                                		</if>
                                	</foreach>                                
                                </div>
                            </td>
                        </div>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!--新订单列表 操作记录信息-->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-center">操作记录</h3>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td class="text-center">操作者</td>
                        <td class="text-center">操作时间</td>
                        <td class="text-center">订单状态</td>
                        <td class="text-center">付款状态</td>
                        <td class="text-center">发货状态</td>
                        <td class="text-center">操作</td>
                        <td class="text-center">备注</td>
                    </tr>
                    </thead>
                    <tbody>
                    <volist name="action_log" id="log">
                        <tr>
                            <td class="text-center">{$log.action_user}</td>
                            <td class="text-center">{$log.log_time|date='Y-m-d H:i:s',###}</td>
                            <td class="text-center">{$order_status[$log[order_status]]}</td>
                            <td class="text-center">{$pay_status[$log[pay_status]]}<if condition="$order['pay_code'] eq 'code'"><span style="color: red">(货到付款)</span></if></td>
                            <td class="text-center">{$shipping_status[$log[shipping_status]]}</td>
                            <td class="text-center">{$log.action_note}</td>
                            <td class="text-center">{$log.status_desc}</td>
                        </tr>
                    </volist>
                    </tbody>
                </table>
            </div>
          </div>
        </div>
	  </div>
    </div> 
   </section>
</div>
</body>
<script>
function pay_cancel(obj){
    var url =  $(obj).attr('data-url')+'&'+Math.random();
    layer.open({
        type: 2,
        title: '退款操作',
        shadeClose: true,
        shade: 0.8,
        area: ['45%', '50%'],
        content: url, 
    });
}
//取消付款
function pay_callback(s){
	if(s==1){
		layer.msg('操作成功', {icon: 1});
		layer.closeAll('iframe');
		location.href =	location.href;
	}else{
		layer.msg('操作失败', {icon: 3});
		layer.closeAll('iframe');
		location.href =	location.href;		
	}
}

// 弹出退换货商品
function selectGoods2(order_id){
	var url = "/index.php?g=Shop&m=Order&a=get_order_goods&order_id="+order_id;
	layer.open({
		type: 2,
		title: '选择商品',
		shadeClose: true,
		shade: 0.8,
		area: ['60%', '60%'],
		content: url, 
	});
}    
// 申请退换货
function call_back(order_id,goods_id)
{
	var url = "/index.php?g=Shop&m=Order&a=add_return_goods&order_id="+order_id+"&goods_id="+goods_id;	
	location.href = url;
}
</script>
</html>