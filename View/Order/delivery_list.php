<include file="Public/min-header"/>
<div class="wrapper">
  <include file="Public/breadcrumb"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 发货单列表</h3>
                </div>
                <div class="panel-body" id="app">
                    <div class="navbar navbar-default">
                            <form action="{:U('Order/ajax_delivery_list')}" id="search-form" class="navbar-form form-inline" method="post" onsubmit="return false">
                                <div class="form-group">
                                    <label class="control-label" for="input-order-id">收货人</label>
                                    <div class="input-group">
                                        <input type="text" name="consignee" value="" placeholder="收货人" id="input-member-id" class="form-control">
                                        <!--<span class="input-group-addon" id="basic-addon2"><i class="fa fa-search"></i></span>-->
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="input-order-id">订单编号</label>
                                    <div class="input-group">
                                        <input type="text" name="order_sn" value="" placeholder="订单 编号" id="input-order-id" class="form-control">
                                        <!--<span class="input-group-addon" id="basic-addon1"><i class="fa fa-search"></i></span>-->
                                    </div>
                                </div>
                        		<div class="form-group">
                                    <select name="shipping_status" class="input-sm">
                                            <option value="0">待发货</option>
                                            <option value="1">已发货</option>
                  							<option value="2">部分发货</option>
                                    </select>
                                </div>
                                <a href="javascript:void(0)" @click="searchBtn" id="button-filter search-order" class="btn btn-primary "><i class="fa fa-search"></i> 筛选</a>
                            </form>
                    </div>
                    <div>
                        <form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <td class="text-center">
                                            <a href="javascript:sort('order_sn');">订单编号</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:sort('add_time');">下单时间</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:sort('consignee');">收货人</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:sort('consignee');">联系电话</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:sort('order_id');">所选物流</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:;">物流费用</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:;">支付时间</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:sort('total_amount');">订单总价</a>
                                        </td>
                                        <td class="text-center">操作</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in orderList">
                                            <td class="text-center">{{item.order_sn}}</td>
                                            <td class="text-center">{{item.add_time | getFormatTime}}</td>
                                            <td class="text-center">{{item.consignee}}</td>
                                            <td class="text-center">{{item.mobile}}</td>
                                            <td class="text-center">{{item.shipping_name}}</td>
                                            <td class="text-center">{{item.shipping_price}}</td>
                                            <td v-if="item.pay_time > 0" class="text-center">{{item.pay_time | getFormatTime}}</td>
                                            <td v-else>货到付款</td>
                                            <td class="text-center">{{item.total_amount}}</td>
                                            <td v-if="item.shipping_status != 1" class="text-center">
                                                    <a :href="'{:U('Shop/Order/delivery_info')}&order_id='+item.order_id" data-toggle="tooltip" title="" class="btn btn-info" data-original-title="查看详情">去发货</a>
                                                    <a :href="'{:U('Shop/Order/delivery_info')}&order_id='+item.order_id" data-toggle="tooltip" title="" class="btn btn-info" data-original-title="查看详情"><i class="fa fa-eye"></i></a>
                                                    <a :href="'{:U('Order/shipping_print')}&order_id='+item.order_id" target="_blank" data-toggle="tooltip" class="btn btn-default" title="打印快递单">
                                                        <i class="fa fa-print"></i>快递单
                                                    </a>
                                            </td>
                                            <td v-else>
                                                <a :href="'{:U('Order/order_print')}&template=picking&order_id='+item.order_id" target="_blank" data-toggle="tooltip" class="btn btn-default" title="打印配货单">
                                                    <i class="fa fa-print"></i>配货单
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                    <div class="dataTables_paginate paging_simple_numbers">
                        <button @click="toPage( parseInt(page) - 1 )" class="btn btn-primary">上一页
                        </button>
                        <button @click="toPage( parseInt(page) + 1 )" class="btn btn-primary">下一页
                        </button>
                        <span style="line-height: 30px;margin-left: 50px"><input id="ipt_page"
                                                                                 style="width:30px;"
                                                                                 type="text"
                                                                                 v-model="temp_page"> / {{ page_count }}</span>
                        <span><button class="btn btn-primary"
                                      @click="toPage( temp_page )">GO</button></span>
                    </div>
                </div>
            </div>
        </div>        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<include file="Public/vue"/>
<script>
    $(document).ready(function(){
        new Vue({
            el: '#app',
            data: {
                'orderList': [],
                page: 1,
                page_count: 1,
                temp_page: 1
            },
            mixins: [window.__baseMethods, window.__baseFilters],
            methods: {
                getList: function () {
                    var that = this
                    that.httpGet('{:U("Shop/Order/getDeliveryList")}&page='+that.page, $('#search-form').serialize(), function (res) {
                        console.log(res);
                        that.orderList = res.orderList;
                        that.page = res.page['page'];
                        that.temp_page = res.page['page'];
                        that.page_count = res.page['page_count'];
                    })
                },
                searchBtn: function (){
                    console.log($('#search-form').serialize());
                    this.getList();
                },
                toPage: function (page) {
                    page = parseInt(page);
                    if (page < 1) {
                        page = 1;
                    }
                    if (page > this.page_count) {
                        page = this.page_count;
                    }
                    this.page = page;
                    this.getList();
                }
            },
            mounted: function(){
                this.getList();
            }
        });
    });
</script>
</body>
</html>