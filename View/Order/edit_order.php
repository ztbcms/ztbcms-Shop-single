<include file="Public/min-header" />
<div class="wrapper">
    <include file="Public/breadcrumb" />
    <section class="content">
        <!-- Main content -->
        <div class="container-fluid">
            <div class="pull-right">
                <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 修改订单信息</h3>
                </div>
                <div class="panel-body">
                    <!--表单数据-->
                    <form method="post" action="{:U('Order/edit_order')}" id="order-add">
                        <div class="tab-pane">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td>费用信息:</td>
                                        <td>
                                            <div class="col-xs-9">
                                                <input type="hidden" name="order_id" value="{$order.order_id}"> 订单总额：{$order.total_amount} = 商品总价：{$order.goods_price}+运费:{$order.shipping_price}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>收货人:</td>
                                        <td>
                                            <div class="form-group">
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
                                    <tr>
                                        <td>地址:</td>
                                        <td>
                                            <div class="form-group ">
                                                <div class="col-xs-2">
                                                    <select onchange="get_city(this)" id="province" name="province" class="form-control">
                                            <option value="0">选择省份</option>
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
                                                    <input name="address" id="address" value="{$order.address}" class="form-control" placeholder="详细地址" />
                                                </div>
                                                <div class="col-xs-2">
                                                    <span id="err_address" style="color:#F00; display:none;">请完善收货地址</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
<!--                                    <tr>-->
<!--                                        <td>配送物流</td>-->
<!--                                        <td>-->
<!--                                            <div class="form-group ">-->
<!--                                                <div class="col-xs-2">-->
<!--                                                    <select id="shipping" name="shipping" class="form-control">-->
<!--                                            <volist name="shipping_list" id="shipping">-->
<!--                                                <option <if condition="$order[shipping_code] eq $shipping[code]">selected</if> value="{$shipping.code}" >{$shipping.name}</option>-->
<!--                                            </volist>-->
<!--                                        </select>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </td>-->
<!--                                    </tr>-->
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
                                            <div class="form-group">
                                                <div class="col-xs-4">
                                                    <input name="invoice_title" value="{$order.invoice_title}" class="form-control" placeholder="发票抬头" />
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>添加商品:</td>
                                        <td>
                                            <div class="form-group">
                                                <div class="col-xs-2">
                                                    <a class="btn btn-primary" href="javascript:void(0);" onclick="selectGoods()"><i class="fa fa-search"></i>添加商品</a>
                                                </div>
                                                <div class="col-xs-2">
                                                    <span id="err_goods" style="color:#F00; display:none;">请添加下单商品</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品列表:</td>
                                        <td>
                                            <div class="form-group">
                                                <div class="col-xs-10">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <td class="text-left">商品名称</td>
                                                                <td class="text-left">规格</td>
                                                                <td class="text-left">价格</td>
                                                                <td class="text-left">数量</td>
                                                                <td class="text-left">操作</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <foreach name="orderGoods" item="vo">
                                                                <tr>
                                                                    <td class="text-left">{$vo.goods_name}</td>
                                                                    <td class="text-left">{$vo.spec_key_name}</td>
                                                                    <td class="text-left">{$vo.goods_price}</td>
                                                                    <td class="text-left">
                                                                        <input type="hidden" name="spec[]" rel="{$vo.goods_id}" value="{$vo.spec_key}">
                                                                        <input type="text" class="input-sm" name="old_goods[{$vo.rec_id}]" value="{$vo.goods_num}" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')"></td>
                                                                    <td class="text-left"><a href="javascript:void(0)" onclick="javascript:$(this).parent().parent().remove();">删除</a></td>
                                                                </tr>
                                                            </foreach>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="form-group">
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
                                                    <textarea style="width:440px; height:150px;" name="admin_note">{$order.admin_note|htmlspecialchars_decode}</textarea>
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
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    /* 用户订单区域选择 */
    // get_province()
    $(document).ready(function() {
        $('#province').val('{$order.province}');
        $('#city').val('{$order.city}');
        $('#district').val('{$order.district}');
        $('#shipping_id').val('$order.shipping_id');
    });
    // 选择商品
    function selectGoods() {
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
    function call_back(table_html) {
        $('#goods_td').empty().html('<table class="table table-bordered">' + table_html + '</table>');
        //过滤选择重复商品
        $('input[name*="spec"]').each(function(i, o) {
            if ($(o).val()) {
                var name = 'goods_id[' + $(o).attr('rel') + '][' + $(o).val() + '][goods_num]';
                $('input[name="' + name + '"]').parent().parent().remove();
            }
        });
        layer.closeAll('iframe');
    }

    function checkSubmit() {
        $("span[id^='err_']").each(function() {
            $(this).hide();
        });
        ($.trim($('#consignee').val()) == '') && $('#err_consignee').show();
        ($.trim($('#province').val()) == '') && $('#err_address').show();
        ($.trim($('#city').val()) == '') && $('#err_address').show();
        ($.trim($('#district').val()) == '') && $('#err_address').show();
        ($.trim($('#address').val()) == '') && $('#err_address').show();
        ($.trim($('#mobile').val()) == '') && $('#err_mobile').show();
        if (($("input[name^='goods_id']").length == 0) && ($("input[name^='old_goods']").length == 0)) {
            layer.alert('订单中至少要有一个商品', {
                icon: 2
            }); // alert('少年,订单中至少要有一个商品');
            return false;
        }
        if ($("span[id^='err_']:visible").length > 0)
            return false;
        $('#order-add').submit();
    }
</script>
</body>

</html>