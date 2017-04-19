<include file="Public/min-header"/>
<div class="wrapper">
    <include file="Public/breadcrumb"/>
    <style>
        #search-form > .form-group {
            margin-left: 10px;
        }
    </style>
    <!-- Main content -->
    <section class="content" id="app">
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 用户优惠券</h3>
                </div>
                <div class="panel-body">
                    <div class="navbar navbar-default">
                        <form action="" id="search-form2" class="navbar-form form-inline" method="post"
                              onsubmit="return false">
                            <button onclick="location.href='{:U('Coupon/add_user_coupon')}'"
                                    class="btn btn-primary pull-right"><i class="glyphicon glyphicon-send"></i> 赠送优惠券
                            </button>
                        </form>
                    </div>
                    <div id="ajax_return">
                        <form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <td class="text-right">
                                            <a href="javascript:;">ID</a>
                                        </td>
                                        <td class="text-left">
                                            <a href="javascript:;">优惠券编码</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:;">优惠券类型</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:;">优惠价格</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:;">满减价格</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:;">优惠券状态</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:;">使用起始时间</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:;">过期时间</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:;">使用时间</a>
                                        </td>
                                        <td class="text-right">操作</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="item in lists">
                                        <td class="text-right">{{ item.id }}</td>
                                        <td class="text-left">{{ item.coupon_num }}</td>
                                        <td class="text-center">
                                            <span v-if="item.type == 0">不可叠加</span>
                                            <span v-if="item.type == 1">可叠加</span>
                                        </td>
                                        <td class="text-center">{{ item.discount_price }}</td>
                                        <td class="text-center">{{ item.full_price }}</td>
                                        <td class="text-center">
                                            <span v-if="item.status == 0">无效</span>
                                            <span v-if="item.status == 1">未使用</span>
                                            <span v-if="item.status == 2">已使用</span>
                                            <span v-if="item.status == 3">过期</span>
                                        </td>
                                        <td class="text-center">{{ item.start_time }}</td>
                                        <td class="text-center">{{ item.end_time }}</td>
                                        <td class="text-center">
                                            <template v-if="item.use_time == 0">0</template>
                                            <template v-else>{{ item.use_time | getFormatTime }}</template>
                                        </td>
                                        <td class="text-right">
                                            <!--<a target="_blank" href="{:U('Home/Goods/goodsInfo',array('id'=>$list['goods_id']))}" class="btn btn-info" title="查看详情"><i class="fa fa-eye"></i></a>-->
                                            <a :href="'{:U('Coupon/edit_user_coupon')}&id='+item.id"
                                               class="btn btn-primary" title="编辑"><i class="fa fa-eye"></i></a>
                                            <a href="javascript:void(0);" v-on:click="delCoupon(item.id)"
                                               class="btn btn-danger" title="删除"><i class="fa fa-trash-o"></i></a>
                                            <!--<a href="javascript:void(0);" onclick="ClearGoodsHtml('{$list[goods_id]}')" class="btn btn-default" title="清除静态缓存页面"><i class="fa fa-fw fa-refresh"></i></a>-->
                                            <!--<a href="javascript:void(0);" onclick="ClearGoodsThumb('{$list[goods_id]}')" class="btn btn-default" title="清除缩略图缓存"><i class="glyphicon glyphicon-picture"></i></a>-->
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>

                        <!--     分页-->
                        <v-page :page="page" v-on:update="getList" :page_count="page_count"></v-page>
                        <!--   /分页-->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<include file="Public/vue"/>
<script>
    $(document).ready(function () {
        new Vue({
            el: '#app',
            data: {
                page: 1,
                total: 0,
                page_count: 1,
                limit:20,
                lists: []
            },
            mixins: [window.__baseMethods,window.__baseFilters],
            methods: {
                getList: function () {
                    var that = this
                    var data = {
                        page:this.page,
                        limit:this.limit
                    }
                    that.httpPost('index.php?g=Shop&m=Coupon&a=user_coupon',data,function (res) {
                        console.log(res)
                        if (res.status) {
                            var data = res.info
                            that.lists = data.lists
                            that.page = data.page
                            that.total = data.total
                            that.page_count = data.page_count
                        }
                    })
                },
                delCoupon:function (id) {
                    var that = this
                    layer.confirm('是否确定删除？', function () {
                        that.httpPost('index.php?g=Shop&m=Coupon&a=delete_user_coupon',{id:id},function (res) {
                            if(res.status == 1){
                                layer.msg(res.msg)
                                setTimeout(function () {
                                    window.location.href = "{:U('Coupon/user_coupon')}";
                                },1500)
                            }else{
                                layer.msg(res.msg)
                            }
                        })
                    })
                }
            },
            mounted: function () {
                this.getList()
            },
            components: {
                'v-page': pageComponent
            }
        })

    });
</script>
</body>

</html>