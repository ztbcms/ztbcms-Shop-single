<include file="Public/min-header"/>
<div class="wrapper">
    <include file="Public/breadcrumb"/>
    <section class="content">
        <!-- Main content -->
        <!--<div class="container-fluid">-->
        <div class="container-fluid" id="app">
            <form id="delivery-form" action="" method="post" onsubmit="return false;">
                <!--新订单列表 基本信息-->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title text-center">基本信息</h3>
                    </div>
                    <div class="panel-body">
                        <nav class="navbar navbar-default">
                            <div class="collapse navbar-collapse">
                                <div class="navbar-form pull-right margin">
                                    <a :href="'{:U('Order/order_print')}&template=picking&order_id='+order.order_id"
                                       target="_blank" data-toggle="tooltip" title="" class="btn btn-primary"
                                       data-original-title="打印订单">
                                        <i class="fa fa-print"></i>打印配货单
                                    </a>
                                    <a href="{:U('Shop/Order/delivery_list')}" data-toggle="tooltip" title=""
                                       class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
                                </div>
                            </div>
                        </nav>
                        <table class="table table-bordered">
                            <tbody>

                            <tr>
                                <td class="text-right">订单号:</td>
                                <td class="text-center">{{order.order_sn}}</td>
                                <td class="text-right">下单时间:</td>
                                <td class="text-center">{{order.add_time|getFormatTime}}</td>
                            </tr>
                            <tr>
                                <td class="text-right">配送方式:</td>
                                <td class="text-center">
                                    <select style="width: 200px;" class="form-control" name="shipping_code"
                                            id="shipping_code">
                                        <option value="shentong">申通</option>
                                        <option value="ems">EMS</option>
                                        <option value="shunfeng">顺丰</option>
                                        <option value="yuantong">圆通</option>
                                        <option value="zhongtong">中通</option>
                                        <option value="yunda">韵达</option>
                                        <option value="tiantian">天天</option>
                                        <option value="huitongkuaidi">汇通</option>
                                        <option value="quanfengkuaidi">全峰</option>
                                        <option value="debangwuliu">德邦</option>
                                        <option value="zhaijisong">宅急送</option>
                                    </select>
                                    <input type="hidden" name="shipping_name" id="shipping_name" value="shentong">
                                </td>
                                <td class="text-right">配送费用:</td>
                                <td class="text-center">{{order.shipping_price}}</td>
                            </tr>
                            <tr>
                                <td class="text-right">配送单号:</td>
                                <td class="text-center">
                                    <input style="width: 200px;" class="form-control" name="invoice_no" id="invoice_no"
                                           v-model="invoice_no">
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
                            <tbody>
                            <tr>
                                <td class="text-right">收货人:</td>
                                <td class="text-center">{{order.consignee}}</td>
                            </tr>
                            <tr>
                                <td class="text-right">地址:</td>
                                <td class="text-center">{{order.address}}</td>
                            </tr>
                            <tr>
                                <td class="text-right">电话:</td>
                                <td class="text-center">{{order.mobile}}</td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
                <!--新订单列表 商品信息-->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title text-center">商品信息</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <td class="text-left">商品</td>
                                <td class="text-left">属性</td>
                                <td class="text-left">购买数量</td>
                                <td class="text-left">商品单价</td>
                                <td class="text-left">选择发货</td>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in orderGoods">
                                    <td class="text-left"><a
                                                :href="'{:U('Goods/addEditGoods')}&id='+item.goods_id">{{item.goods_name}}</a>
                                    </td>
                                    <td class="text-left">{{item.spec_key_name}}</td>
                                    <td class="text-left">{{item.goods_num}}</td>
                                    <td class="text-left">{{item.goods_price}}</td>
                                    <td v-if="item.is_send == 1" class="text-left">已发货</td>
                                    <td v-else class="text-left">
                                        <input type="checkbox" name="goods[]" :value="item.rec_id"
                                               checked="checked">
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
                <!--发货状态下课修改订单号-->
                <if condition="$order['shipping_status'] neq 1">
                    <!--新订单列表 操作信息-->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title text-center">发货信息</h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td class="text-right col-sm-2 margin">发货单备注：</td>
                                    <td colspan="3">
                                        <input type="hidden" name="order_id" :value="order.order_id">
                                        <textarea name="note" placeholder="请输入操作备注" rows="3"
                                                  class="form-control" v-model="order.note"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <div class="form-group text-center">
                                            <button @click="dosubmit()" class="btn btn-primary" type="button">确认发货
                                            </button>
                                            <button onclick="history.go(-1)" class="btn btn-primary" type="button">返回
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </if>

                <!--新订单列表 操作记录信息-->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title text-center">发货记录</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <td class="text-center">发货时间</td>
                                <td class="text-center">发货单号</td>
                                <td class="text-center">收货人</td>
                                <td class="text-center">快递公司</td>
                                <td class="text-center">备注</td>
                                <td class="text-center">查看</td>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in delivery_record">
                                    <td class="text-center">{{item.create_time|getFormatTime}}</td>
                                    <td class="text-center">{{item.invoice_no}}</td>
                                    <td class="text-center">{{item.consignee}}</td>
                                    <td class="text-center">{{item.shipping_name}}</td>
                                    <td class="text-center">{{item.note}}</td>
                                    <td class="text-center"><a href="http://www.kuaidi100.com/" target="_blank">查看物流</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
<include file="Public/vue"/>
<script>
    new Vue({
        el: '#app',
        data: {
            'order': {},
            'orderGoods': [],
            'delivery_record': [],
            'invoice_no': '',
        },
        mixins: [window.__baseMethods, window.__baseFilters],
        methods: {
            getDetail: function(){
                var that = this;
                that.httpPost('{:U("Shop/Order/delivery_info")}', {'order_id': '<?php echo $order_id;?>'}, function (res) {
                    that.order = res.order;
                    that.orderGoods = res.orderGoods;
                    that.delivery_record = res.delivery_record;
                })
            },
            dosubmit: function(){
                var that = this;
                if (!that.order['invoice_no']){
                    layer.alert('请输入配送单号', {icon: 2});
                    return;
                }
                var goods = [];
                $('input[name="goods[]"]:checked').each(function(i){
                    goods[i] = $(this).val();
                });
                if (goods.length === 0) {
                    layer.alert('请选择发货商品', {icon: 2});
                    return;
                }

                //order_id,goods,shipping_code,shipping_name,note 参数
                var data = {
                    'order_id': that.order.order_id,
                    'goods': goods,
                    'shipping_code': $("#shipping_code").val(),
                    'shipping_name': $("#shipping_code option:selected").html(),
                    'note': that.order.note,
                    'invoice_no' : that.invoice_no
                };
                that.httpPost('{:U("Shop/Order/deliveryHandle")}', data, function (res) {
                    layer.alert(res.msg, {
                        icon: res.icon
                    });
                    setTimeout(function(){
                        window.location.reload();
                    }, 700)
                });
            }
        },
        mounted: function(){
            this.getDetail();
        }
    });
</script>
<script>
/*    $('#shipping_code').change(function () {
        var text = $("#shipping_code").find("option:selected").text();
        $("#shipping_name").val(text)
    })
    function dosubmit() {
        if ($('#invoice_no').val() == '') {
            layer.alert('请输入配送单号', {icon: 2});  // alert('请输入配送单号');
            return;
        }
        var a = [];
        $('input[name*=goods]').each(function (i, o) {
            if ($(o).is(':checked')) {
                a.push($(o).val());
            }
        });
        if (a.length == 0) {
            layer.alert('请选择发货商品', {icon: 2});  //alert('请选择发货商品');
            return;
        }
        $('#delivery-form').submit();
    }*/
</script>
</body>
</html>