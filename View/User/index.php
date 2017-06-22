<include file="Public/min-header"/>
<div class="wrapper">
    <include file="Public/breadcrumb"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 用户列表</h3>
                </div>
                <div class="panel-body" id="app">
                    <div class="navbar navbar-default">
                        <form action="" id="search-form2" class="navbar-form form-inline" method="post"
                              onsubmit="return false">
                            <div class="form-group">
                                <label class="control-label" for="input-mobile">手机号码</label>
                                <div class="input-group">
                                    <input type="text" name="mobile" v-model="where.phone" placeholder="手机号码"
                                           id="input-mobile" class="form-control">
                                    <!--<span class="input-group-addon" id="basic-addon2"><i class="fa fa-search"></i></span>-->
                                </div>
                            </div>
                            <div class="form-group">
                                <button @click="getList()" id="button-filter search-order"
                                        class="btn btn-primary pull-right"><i class="fa fa-search"></i> 筛选
                                </button>
                            </div>
                            <a href="{:U('User/add_user')}" class="btn btn-info pull-right">添加会员</a>
                        </form>
                    </div>
                    <div>
                        <form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <td class="text-right">
                                            <a href="javascript:" @click="orderBy('userid');">ID</a>
                                        </td>
                                        <td class="text-left">
                                            <a href="javascript:" @click="orderBy('nickname');">会员昵称</a>
                                        </td>
                                        <td class="text-left">
                                            <a href="javascript:">手机号码</a>
                                        </td>
                                        <td class="text-left">
                                            <a href="javascript:" @click="orderBy('level');">等级</a>
                                        </td>
                                        <td class="text-left">
                                            <a href="javascript:void(0);">一级上线</a>
                                        </td>
                                        <td class="text-left">
                                            <a href="javascript:void(0);">二级上线</a>
                                        </td>
                                        <td class="text-left">
                                            <a href="javascript:void(0);">三级上线</a>
                                        </td>
                                        <td class="text-left">
                                            <a href="javascript:void(0);">直接上线</a>
                                        </td>
                                        <td class="text-left">
                                            <a href="javascript:" @click="orderBy('lastdate');">最后登录时间</a>
                                        </td>
                                        <td class="text-right">操作</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="item in lists">
                                        <td class="text-right">{{item.userid}}</td>
                                        <td class="text-left">{{item.nickname}}</td>
                                        <td class="text-left">{{item.mobile}}
                                            <span v-if="item.mobile_validated == 0 && item.mobile">
                                                    (未验证)
                                                </span>
                                        </td>
                                        <td class="text-left">
                                            {{ item.level > 0 ? level[item.level] : '暂无' }}
                                        </td>
                                        <td class="text-left">{{item.first_leader}}</td>
                                        <td class="text-left">{{item.second_leader}}</td>
                                        <td class="text-left">{{item.third_leader}}</td>
                                        <td class="text-left">{{item.direct_leader}}</td>
                                        <td class="text-left">{{getFormatTime(item.lastdate)}}</td>
                                        <td class="text-right">
                                            <a :href="'{:U('Shop/User/detail')}&id='+item.userid" data-toggle="tooltip"
                                               title="" class="btn btn-info" data-original-title="查看详情"><i
                                                        class="fa fa-eye"></i></a>
                                            <a :href="'{:U('Shop/User/address')}&id='+item.userid" data-toggle="tooltip"
                                               title="" class="btn btn-info" data-original-title="收货地址"><i
                                                        class="fa fa-home"></i></a>
                                            <!--<a href="{:U('Admin/order/index',array('userid'=>$list['userid']))}" data-toggle="tooltip" title="" class="btn btn-info" data-original-title="订单查看"><i class="fa fa-shopping-cart"></i></a>-->
                                            <!--<a href="{:U('Shop/User/delete',array('id'=>$list['userid']))}" id="button-delete6" data-toggle="tooltip" title="" class="btn btn-danger" data-original-title="删除"><i class="fa fa-trash-o"></i></a>-->
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                        <!--     分页-->
                        <v-page :page="page" @update="getList" :page_count="page_count"></v-page>
                        <!--   /分页-->
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
    $(document).ready(function () {
        var vue = new Vue({
            el: '#app',
            data: {
                lists: [],
                level: [],
                page: 1,
                page_count: 1,
                order: 'userid',
                temp_order: 'userid',
                sort: ' asc',
                limit: 20,
                where: {
                    'phone': ''
                }
            },
            methods: {
                getList: function () {
                    var that = this;
                    $.ajax({
                        url: "{:U('User/ajaxindex')}",
                        data: {'page': that.page, 'limit': that.limit, 'order': that.order, 'where': that.where},
                        dataType: 'json',
                        success: function (res) {
                            console.log(res);
                            that.lists = res.data;
                            that.level = res.level;
                            that.page = res.page;
                            that.limit = res.limit;
                            that.page_count = res.page_count;
                            that.limit = res.limit
                        }
                    });
                },
                getFormatTime: function (date) {
                    if (date == 0) {
                        return '未登录';
                    }
                    var time = new Date(parseInt(date * 1000));
                    var y = time.getFullYear();
                    var m = time.getMonth() + 1;
                    var d = time.getDate();
                    var h = time.getHours();
                    var i = time.getMinutes();
                    var res = y + '-' + (m < 10 ? '0' + m : m) + '-' + (d < 10 ? '0' + d : d) + ' ';
                    res += '  ' + (h < 10 ? '0' + h : h) + ':' + (i < 10 ? '0' + i : i);
                    return res;
                },
                toPage: function (page) {
                    page = parseInt(page);
                    if (page < 1) {
                        page = 1;
                    }
                    if (page > vue.page_count) {
                        page = vue.page_count;
                    }
                    if ((vue.page == 1 && page <= 1) || (vue.page == vue.page_count && page >= vue.page_count)) {

                    } else {
                        vue.page = page;
                        this.getList();
                    }

                },
                orderBy: function (field) {
                    if (vue.temp_order == field) {
                        if (vue.sort == ' desc') {
                            vue.sort = ' asc';
                        } else {
                            vue.sort = ' desc';
                        }
                    }
                    vue.temp_order = field;
                    field = field + vue.sort;
                    vue.order = field;
                    this.getList();
                }
            },
            mounted: function () {
                this.getList();
            }, components: {
                'v-page': pageComponent
            }
        });
    });

</script>
</body>
</html>