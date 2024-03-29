<include file="Public/min-header"/>
<div class="wrapper">
  <include file="Public/breadcrumb"/>
    <section class="content">
        <!-- Main content -->
        <div class="container-fluid">
            <div class="pull-right">
                <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i>订单基本信息</h3>
                </div>
                <div class="panel-body">

                    <!--表单数据-->
                    <form method="post" action="{:U('Shop/Order/add_order')}" id="order-add">
                        <div class="tab-pane" >
                            <table class="table table-bordered">
                                <tbody><tr>
                                    <td>用户名:</td>
                                    <td>
                                    <div class="form-group ">
                                        <div class="col-xs-2">
                                            <input name="user_name" id="user_name" class="form-control" value="" placeholder="手机或邮箱搜索" />
                                         </div>   
                                        <div class="col-xs-2">                                         
                                            <select name="user_id" id="user_id" class="form-control">
                                                <option value="0">匿名用户</option>
                                            </select>
                                        </div>
                                        <div class="col-xs-2">
					                          <button class="btn btn-info" type="button" onclick="search_user();"><i class="ace-icon fa fa-search bigger-110"></i>搜索</button>
                                         </div>                                        
                                     </div>                                 
                                    </td>
                                </tr>
                                <tr>
                                    <td>收货人:</td>
                                    <td>
                                    <div class="form-group ">
	                                    <div class="col-xs-2">
	                                        <input name="consignee" id="consignee" value="{$order.consignee}" class="form-control" placeholder="收货人名字" />	                                    
                                        </div>
                                        <div class="col-xs-2">
										    <span id="err_consignee" style="color:#F00; display:none;">收货人名字不能为空</span>
                                        </div>
                                    </div>    
                                    </td>
                                </tr>                                
                                <tr>
                                    <td>地址:</td>
                                    <td>
                                    <div class="form-group ">
                                    <div class="col-xs-2">
                                        <select onchange="get_city(this)" id="province" name="province" class="form-control">
                                            <option  value="0">选择省份</option>
                                            <volist name="province" id="vo">
                                                <option value="{$vo.id}" >{$vo.areaname}</option>
                                            </volist>
                                        </select>
                                         </div>   
                                        <div class="col-xs-2">                                        
                                        <select onchange="get_area(this)" id="city" name="city" class="form-control">
                                            <option value="0">选择城市</option>
                                            <volist name="city" id="vo">
                                                <option value="{$vo.id}">{$vo.areaname}</option>
                                            </volist>
                                        </select>
                                         </div>   
                                        <div class="col-xs-2">                                        
                                        <select id="district" name="district" class="form-control">
                                            <option value="0">选择区域</option>
                                            <volist name="area" id="vo">
                                                <option value="{$vo.id}">{$vo.areaname}</option>
                                            </volist>
                                        </select>
                                         </div>   
                                        <div class="col-xs-3">
                                        <input name="address" id="address" value="{$order.address}" class="form-control"   placeholder="详细地址"/>
									    </div>   
										<div class="col-xs-2">
										    <span id="err_address" style="color:#F00; display:none;">请完善收货地址</span>
                                        </div>                                                                             
									</div>  
                                    </td>
                                </tr>
                                <tr>
                                    <td>手机:</td>
                                    <td>
                                    <div class="form-group ">
	                                    <div class="col-xs-2">
	                                        <input name="mobile" id="mobile" value="{$order.mobile}" class="form-control" placeholder="收货人联系电话" />
                                        </div>
										<div class="col-xs-2">
										    <span id="err_mobile" style="color:#F00; display:none;">收货人电话不能为空</span>
                                        </div>                                                                                                                     
                                    </div>    
                                    </td>
                                </tr>
<!--                                <tr>-->
<!--                                    <td>配送物流</td>-->
<!--                                    <td>-->
<!--                                    <div class="form-group ">-->
<!--	                                    <div class="col-xs-2">-->
<!--                                        <select id="shipping" name="shipping"  class="form-control" >-->
<!--                                            <volist name="shipping_list" id="shipping">-->
<!--                                                <option <if condition="$order[shipping_code] eq $shipping[code]">selected</if> value="{$shipping.code}" >{$shipping.name}</option>-->
<!--                                            </volist>-->
<!--                                        </select>-->
<!--                                        </div>-->
<!--                                    </div>   -->
<!--                                    </td>-->
<!--                                </tr>-->
                                <tr>
                                    <td>支付方式</td>
                                    <td>
                                    <div class="form-group ">
	                                    <div class="col-xs-2">
                                        <select id="payment" name="payment"  class="form-control" >
                                            <?php foreach ($payment_list as $k=>$vo):?>
                                                <option <if condition="$order[pay_code] eq $k">selected</if> value="{$k}" >{$vo}</option>
                                            <?php endforeach;?>
                                        </select>
                                        </div>
                                    </div>   
                                    </td>
                                </tr>
                                <tr>
                                    <td>发票抬头:</td>
                                    <td>
                                    <div class="form-group ">
	                                    <div class="col-xs-4">
	                                        <input name="invoice_title" value="{$order.invoice_title}" class="form-control"  placeholder="发票抬头"/>
                                        </div>
                                    </div>    
                                    </td>
                                </tr>                                
                                <tr>
                                    <td>添加商品:</td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-xs-2">                                        
	                                            <a class="btn btn-primary" href="javascript:void(0);" onclick="selectGoods()" ><i class="fa fa-search"></i>添加商品</a>
                                            </div>                                                            
                                            <div class="col-xs-2">
                                                <span id="err_goods" style="color:#F00; display:none;">请添加下单商品</span>
                                            </div>                                            
                                        </div>                                    
                                    </td>
                                </tr>                                                                                          
                                <tr>
                                    <td>商品:</td>
                                    <td>                                    
                                       <div class="form-group ">                                       
                                            <div class="col-xs-10" id="goods_td">
                                                
                                            </div>                                                                                                                                                      
	                                   </div>                                                                      
                                    </td>
                                </tr>                                 
                                <tr>
                                    <td>管理员备注:</td>
                                    <td>
                                    <div class="form-group ">
	                                    <div class="col-xs-4">
                                        	<textarea style="width:440px; height:150px;" name="admin_note">管理员添加订单</textarea>
                                        </div>
                                    </div>    
                                    </td>
                                </tr>                                  
                                
                                </tbody>
                                </table>
                        </div>
                        <input type="hidden" name="id" value="{$order.order_id}">
                        <button class="btn btn-info" type="button" onclick="checkSubmit()">
                            <i class="ace-icon fa fa-check bigger-110"></i>
                            保存
                        </button>
                    </form> 
                    <script>
                        function checkSubmit()
						{							
							$("span[id^='err_']").each(function(){
								$(this).hide();
							});

						   ($.trim($('#consignee').val()) == '') && $('#err_consignee').show();
						   ($.trim($('#province').val()) == '') && $('#err_address').show();
						   ($.trim($('#city').val()) == '') && $('#err_address').show();
						   ($.trim($('#district').val()) == '') && $('#err_address').show();
						   ($.trim($('#address').val()) == '') && $('#err_address').show();
						   ($.trim($('#mobile').val()) == '') && $('#err_mobile').show();
						   ($("input[name^='goods_id']").length ==0) && $('#err_goods').show();

						   if($("span[id^='err_']:visible").length > 0 )
						      return false;
							  
						   $('#order-add').submit();	  
						}
                    </script>
                </div>
            </div>
<!--订单商品信息--> 
			
<!--订单商品信息 end-->
            
            
        </div>    <!-- /.content -->
    </section>
</div>
<script>
    // 搜索用户 
    function search_user()
	{
		var user_name = $('#user_name').val();
		if($.trim(user_name) == '')
			return false;
			
			 $.ajax({
                type : "POST",
                url:"/index.php?g=Shop&m=User&a=search_user",//+tab,
                data :{search_key:$('#user_name').val()},// 你的formid
                success: function(data){
					data = data + '<option value="0">匿名用户</option>';
					$('#user_id').html(data);
                }
            });		
	}


    /* 用户订单区域选择 */
    $(document).ready(function(){
		/*
		$('#province').val({$order.province});
		$('#city').val({$order.city});
		$('#district').val({$order.district});
		$('#shipping_id').val({$order.shipping_id});
		*/
    })
// 选择商品
function selectGoods(){
    var url = "{:U('Shop/Order/search_goods')}";
    layer.open({
        type: 2,
        title: '选择商品',
        shadeClose: true,
        shade: 0.8,
        area: ['60%', '60%'],
        content: url, 
    });
}
// 选择商品返回
function call_back(table_html)
{
	$('#goods_td').empty().html('<table class="table table-bordered">'+table_html+'</table>');
	layer.closeAll('iframe');
}
</script>
</body>
</html>