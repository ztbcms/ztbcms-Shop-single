<include file="Public/min-header" />
<div class="wrapper">
	<include file="Public/breadcrumb"/>		
    <section class="content ">
        <!-- Main content -->
        <div class="container-fluid">
            <div class="pull-right">
                <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
            	<a href="javascript:" class="btn btn-default" data-url="http://www.tp-shop.cn/Doc/Index/article/id/1020/developer/user.html" onclick="get_help(this)"><i class="fa fa-question-circle"></i> 帮助</a>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i>退换货</h3>
                </div>
                <div class="panel-body ">   
                    <!--表单数据-->
                    <form method="post" id="return_form" action="{:U('Order/return_info')}">                    
                        <!--通用信息-->
                    <div class="tab-content col-md-10">                 	  
                        <div class="tab-pane active" id="tab_tongyong">                           
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td class="col-sm-2">订单编号：</td>
                                    <td class="col-sm-8">
                                        <a href="{:U('Admin/order/detail',array('order_id'=>$return_goods['order_id']))}">{$return_goods.order_sn}</a>
                                    </td>
                                </tr>  
                                <tr>
                                    <td>用户：</td>
                                    <td>                    
					                    {$user.nickname}
                                    </td>
                                </tr>  
                                <tr>
                                    <td>申请日期：</td>
                                    <td>                    
					                    {$return_goods.addtime|date="Y-m-d H:i",###}
                                    </td>
                                </tr>                                  
                                <tr>
                                    <td>商品名称：</td>
                                    <td >
                         				<!--<a href="{:U('Home/Goods/goodsInfo',array('id'=>$return_goods[goods_id]))}" target="_blank">{$goods[goods_name]}</a>                      -->
                                         {$goods[goods_name]}
                                    </td>
                                </tr>  
                                <tr>
                                    <td>退换货：</td>
                                    <td>
                                     <div class="form-group col-xs-3">
										<select  name="type"  class="form-control">
                                             <option value="0" <if condition="$return_goods['type'] eq 0">selected="selected"</if>>退货</option>
                                             <option value="1" <if condition="$return_goods['type'] eq 1">selected="selected"</if>>换货</option>
                                        </select>
                                      </div>
                                          <a href="{:U('Shop/User/account_edit',array('id'=>$return_goods[user_id],'user_money'=>$goods[shop_price],'desc'=>'退货退款到余额'))}">                                          
                                          <input class="btn btn-primary" type="button"  value="退款到用户余额">
                                          </a>
                                      
                                    </td>
                                </tr>  
                                <tr>
                                    <td>退货描述：</td>
                                    <td>                    
					                    <textarea name="reason" id="reason" cols="" rows="" readonly="readonly" class="area" style="width:400px; height:120px;">{$return_goods['reason']}</textarea>                                        
                                    </td>
                                </tr>  
                                <tr>
                                    <td>用户上传照片：</td>
                                    <td>
                                         <volist name="return_goods[imgs]" id="item">  
                                             <a href="{$item}" target="_blank"><img src="{$item}" width="85" height="85" /></a>&nbsp;&nbsp;&nbsp;                            
                                         </volist>                      					
                                    </td>
                                </tr>                                    
                                <tr>
                                    <td>状态：</td>
                                    <td>
                                    <div class="form-group  col-xs-3">
										<select class="form-control" name="status">
                                             <option value="0" <if condition="$return_goods['status'] eq 0">selected="selected"</if>>未处理</option>
                                             <option value="1" <if condition="$return_goods['status'] eq 1">selected="selected"</if>>处理中</option>
                                             <option value="2" <if condition="$return_goods['status'] eq 2">selected="selected"</if>>已完成</option>                                             
                                        </select>
                                         </div>

                                    </td>
                                </tr>     
                                <tr>
                                    <td>处理备注：</td>
                                    <td>                    
					                    <textarea name="remark" id="remark" cols="" rows=""  class="area" style="width:400px; height:120px;">{$return_goods['remark']}</textarea>                                        
                                    </td>
                                </tr>                                                                                                                                                          
                                </tbody> 
                                <tfoot>
                                	<tr>
                                	<td><input type="hidden" name="id" value="{$id}">
                                	</td>
                                	<td class="text-right"><input class="btn btn-primary" type="submit"  value="保存"></td></tr>
                                </tfoot>                               
                                </table>
                        </div>                           
                    </div>              
			    	</form><!--表单数据-->
                </div>
            </div>
        </div>
    </section>
</div>
<script>
function adsubmit(){
	$('#handleposition').submit();
}
</script>
</body>
</html>