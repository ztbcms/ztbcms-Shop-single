<include file="Public/min-header"/>
<div class="wrapper">
    <include file="Public/breadcrumb"/>
    <section class="content">
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 品牌列表</h3>
                </div>
                <div class="panel-body" id="app">
                    <div class="navbar navbar-default">
                        <form id="search-form2" class="navbar-form form-inline" method="post" action=""
                              onsubmit="return false;">
                            <div class="form-group">
                                <label for="input-order-id" class="control-label">名称:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="input-order-id" placeholder="搜索词"
                                           v-model="where.keyword" name="keyword">
                                </div>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" id="button-filter search-order" type="submit"
                                        v-on:click="getList()"><i class="fa fa-search"></i> 筛选
                                </button>
                            </div>
                            <a href="javascript:;" @click="addBtn" type="button" class="btn btn-primary pull-right">
                                <i class="fa fa-plus"></i> 添加品牌
                            </a>
                        </form>
                    </div>

                    <div id="ajax_return">

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="sorting text-center">ID</th>
                                    <th class="sorting text-center">品牌名称</th>
                                    <th class="sorting text-center">Logo</th>
                                    <th class="sorting text-center">品牌分类</th>
                                    <th valign="middle">是否推荐</th>
                                    <th class="sorting text-center">排序</th>
                                    <th class="sorting text-center">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in brandList">
                                    <td class="text-center">{{item.id}}</td>
                                    <td class="text-center">{{item.name}}</td>
                                    <td class="text-center">
                                        <a><img width="32" height="32" :src="item.logo"/></a>
                                    </td>
                                    <td class="text-center">{{catList[item.parent_cat_id]}} {{catList[item.cat_id]}}
                                    </td>
                                    <td>
                                        <img v-on:click="changeHot(item)" width="20" height="20"
                                             v-bind:src="item.is_hot == 1 ? '{$config_siteurl}statics/extres/shop/images/yes.png' : '{$config_siteurl}statics/extres/shop/images/cancel.png'"/>
                                    </td>
                                    <td class="text-center">
                                        <input type="text" class="form-control input-sm" v-on:change="updateSort(item)"
                                               size="4" v-model="item.sort"/>
                                    </td>
                                    <td class="text-center">
                                        <a @click="editBtn(item.id)" href="javascript:;" data-toggle="tooltip"
                                           title="" class="btn btn-primary" data-original-title="编辑"><i
                                                    class="fa fa-pencil"></i></a>
                                        <a v-on:click="del(item.id)" href="javascript:;" id="button-delete6"
                                           data-toggle="tooltip" title="" class="btn btn-danger"
                                           data-original-title="删除"><i class="fa fa-trash-o"></i></a></td>
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
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<include file="Public/vue"/>
<script>
    new Vue({
        el: '#app',
        data: {
            where: {},
            catList: [],
            brandList: [],
            page: 1,
            page_count: 1,
        },
        mixins: [window.__baseMethods],
        methods: {
            editBtn: function (id) {
                var that = this
                layer.open({
                    title: '编辑类型',
                    type: 2,
                    area: ['800px', '630px'],
                    fixed: false, //不固定
                    maxmin: false,
                    content: "{:U('Brand/getBrandDetail')}&id=" + id,
                    end: function () {
                        that.getList()
                    }
                });
            },
            addBtn: function () {
                var that = this
                layer.open({
                    title: '添加类型',
                    type: 2,
                    area: ['800px', '630px'],
                    fixed: false, //不固定
                    maxmin: false,
                    content: "{:U('Brand/getBrandDetail')}",
                    end: function () {
                        that.getList()
                    }
                });
            },
            getList: function () {
                var that = this
                that.where.page=that.page
                that.httpGet("{:U('Brand/index')}", that.where, function (res) {
                    that.catList = res.cat_list;
                    that.brandList = res.brandList;
                    that.page = res.page['page'];
                    that.page_count = res.page['page_count'];
                }, false)
            },
            changeHot: function (obj) {
                var that = this
                if (obj.is_hot == 1) {
                    obj.is_hot = 0;
                } else {
                    obj.is_hot = 1;
                }
                that.changeTableVal('ShopBrand', 'id', obj.id, 'is_hot', obj.is_hot)
            },
            updateSort: function (obj) {
                var that = this
                that.changeTableVal('ShopBrand', 'id', obj.id, 'sort', obj.sort)
            },
            del: function (id) {
                var that = this
                layer.confirm('确定要删除该品牌吗？', {
                    btn: ['确定', '取消']
                }, function () {
                    that.httpPost("{:U('Brand/delBrand')}", {'id': id}, function (res) {
                        layer.alert(res.info, {
                            icon: res.status
                        }, function () {
                            layer.closeAll()
                            that.getList()
                        });
                    })
                });
            },
            toPage: function (page) {
                page = parseInt(page);
                if (page < 1) {
                    page = 1;
                }
                if (page > this.page_count) {
                    page = this.page_count;
                }
                this.where = $.extend({}, this.where, {'page': page}); // 合并对象
                this.getList();
            }
        },
        mounted: function () {
            this.getList();
        },
        components: {
            'v-page': pageComponent
        }
    });
</script>
</body>
</html>