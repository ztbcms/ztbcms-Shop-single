<include file="Public/min-header"/>
<link href="{$config_siteurl}statics/extres/shop/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet"
      type="text/css"/>
<script src="{$config_siteurl}statics/extres/shop/plugins/daterangepicker/moment.min.js"
        type="text/javascript"></script>
<script src="{$config_siteurl}statics/extres/shop/plugins/daterangepicker/daterangepicker.js"
        type="text/javascript"></script>
<div class="wrapper">
    <!-- Content Header (Page header) -->
    <include file="Public/breadcrumb"/>
    <!-- Main content -->
    <section class="content" id="app">
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 订单列表</h3>
                </div>
                <div class="panel-body">
                    <div class="navbar navbar-default">
                        <form action="{:U('Order/export_order')}" id="search-form" class="navbar-form form-inline"
                              method="post">
                            <div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" name="consignee" placeholder="收货人"
                                               class="form-control" style="width:150px;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" name="order_sn" placeholder="订单编号"
                                               class="form-control" style="width:150px;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" name="timegap" value="{$timegap}" placeholder="下单日期"
                                               id="add_time" class="form-control" style="width: 200px;">
                                    </div>
                                </div>
                            </div>
                            <div style="margin-top: 10px;">
                                <div class="form-group">
                                    <select name="pay_status" class="form-control" style="width:150px;">
                                        <option value="">支付状态</option>
                                        <option value="0">未支付</option>
                                        <option value="1">已支付</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select name="pay_code" class="form-control" style="width:150px;">
                                        <option value="">支付方式</option>
                                        <option value="alipay">支付宝支付</option>
                                        <option value="wxpay">微信支付</option>
                                        <option value="cod">货到付款</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select name="shipping_status" class="form-control" style="width:150px;">
                                        <option value="">发货状态</option>
                                        <option value="0">未发货</option>
                                        <option value="1">已发货</option>
                                        <option value="2">部分发货</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select name="order_status" class="form-control" style="width:150px;">
                                        <option value="">订单状态</option>
                                        <volist name="order_status" id="v" key="k">
                                            <option value="{$k-1}">{$v}</option>
                                        </volist>
                                    </select>
                                    <input type="hidden" name="order_by" value="order_id">
                                    <input type="hidden" name="sort" value="desc">
                                    <input type="hidden" name="user_id" value="{$_GET[user_id]}">
                                </div>
                            </div>
                            <div style="margin-top: 10px;">
                                <div class="form-group">
                                    <a href="javascript:void(0)" @click="searchBtn" id="button-filter search-order"
                                       class="btn btn-primary">
                                        <i class="fa fa-search"></i>
                                        筛选</a>
                                </div>
                                <div class="form-group">
                                    <a href="/index.php?g=Shop&m=Order&a=add_order" class="btn btn-primary">
                                        <i class="fa fa-search"></i>
                                        添加订单
                                    </a>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-default pull-right"><i
                                                class="fa fa-file-excel-o"></i>&nbsp;导出excel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="ajax_return">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" style="font-size:12px;">
                                <thead>
                                <tr>
                                    <td class="text-center">
                                        <a href="javascript:;">订单编号</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:;">收货人</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:;">总金额</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:;">应付金额</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:;">订单状态</a>
                                    </td>
                                    <td class="text-center">支付状态</td>
                                    <td class="text-center">发货状态</td>
                                    <td class="text-center">支付方式</td>
                                    <td class="text-center">
                                        <a href="javascript:;">下单时间</a>
                                    </td>
                                    <td class="text-center">操作</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in lists">
                                    <td class="text-center">{{ item.order_sn }}</td>
                                    <td class="text-center">{{ item.consignee }}:{{ item.mobile }}</td>
                                    <td class="text-center">{{ item.goods_price }}</td>
                                    <td class="text-center">{{ item.order_amount }}</td>
                                    <td class="text-center">{{ order_status[item.order_status] }}</td>
                                    <td class="text-center">{{ pay_status[item.pay_status] }}</td>
                                    <td class="text-center">{{ shipping_status[item.shipping_status] }}</td>
                                    <td class="text-center">{{ item.pay_code | getPayNameByCode }}</td>
                                    <td class="text-center">{{ item.add_time | getFormatTime }}</td>
                                    <td class="text-center">
                                        <a :href=" '{:U('Order/detail')}&order_id='+item.order_id "
                                           data-toggle="tooltip" title="" class="btn btn-info"
                                           data-original-title="查看详情">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a @click="delOrder(item)" href="javascript:;" data-toggle="tooltip"
                                           class="btn btn-danger" title="删除">
                                            <i class="fa fa-trash-o"></i>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!--     分页-->
                        <v-page :page="page" v-on:update="getList" :page_count="page_count"></v-page>
                        <!--   /分页-->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<include file="Public/vue"/>
<script>
    $(document).ready(function () {
        new Vue({
            el: '#app',
            data: {
                lists: [],
                page: 1,
                limit: 20,
                order_status: {},
                pay_status: {},
                shipping_status: {},
                page: 1,
                page_count: 0
            },
            mixins: [window.__baseMethods, window.__baseFilters],
            methods: {
                searchBtn: function () {
                    this.getList()
                },
                getList: function () {
                    var that = this
                    var params=$('#search-form').serializeArray()
                    params.push({name:'page',value:that.page})
                    console.log(params)
                    that.httpGet('{:U("Shop/Order/orderList")}',params, function (res) {
                        var data = res.info
                        that.shipping_status = data.shipping_status
                        that.pay_status = data.pay_status
                        that.order_status = data.order_status
                        that.lists = data.lists
                        that.page = data.page
                        that.page_count = data.page_count
                    })
                },
                delOrder: function (item) {
                    var that = this
                    if (item.order_status == 3 || item.order_status == 5) {
                        layer.msg('该订单状态不允许删除')
                        return;
                    }
                    var id = layer.confirm('是否确定删除？', function () {
                        that.httpPost('{:U("Shop/Order/delete_order")}', {order_id: item.order_id}, function (res) {
                            console.log(res)
                            if (res.status) {
                                that.getList()
                                layer.msg(res.info)
                                layer.close(id)
                            } else {
                                layer.msg(res.info)
                            }
                        })
                    })
                }
            },
            mounted: function () {
                var that = this
                that.getList()
            },
            components: {
                'v-page': pageComponent
            }
        })
        $('#add_time').daterangepicker({
            format: "YYYY/MM/DD",
            singleDatePicker: false,
            showDropdowns: true,
            minDate: '2016/01/01',
            maxDate: '2030/01/01',
            startDate: '2016/01/01',
            locale: {
                applyLabel: '确定',
                cancelLabel: '取消',
                fromLabel: '起始时间',
                toLabel: '结束时间',
                customRangeLabel: '自定义',
                daysOfWeek: ['日', '一', '二', '三', '四', '五', '六'],
                monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
                firstDay: 1
            }
        });
    });
</script>
</body>
</html>