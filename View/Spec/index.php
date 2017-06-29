<include file="Public/min-header"/>
<div class="wrapper">
    <include file="Public/breadcrumb"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 商品规格</h3>
                </div>
                <div class="panel-body" id="app">
                    <div class="navbar navbar-default">
                        <form action="" id="search-form2" class="navbar-form form-inline" method="post"
                              onsubmit="return false">
                            <div class="form-group">
                                <select @change="filter" name="type_id" id="type_id" class="form-control">
                                    <option value="">所有分类</option>
                                    <option v-for="item in goodsTypeList" :value="item.id">{{item.name}}</option>
                                </select>
                            </div>
                            <button type="submit" @click="addBtn" id="button-filter2"
                                    class="btn btn-primary pull-right">
                                <i class="fa fa-plus"></i> 添加规格
                            </button>
                        </form>

                    </div>
                    <div id="ajax_return">
                        <form method="post" enctype="multipart/form-data" target="_blank" id="form-goodsType">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="sorting text-left">ID</th>
                                        <th class="sorting text-left">规格类型</th>
                                        <th class="sorting text-left">规格名称</th>
                                        <th class="sorting text-left">规格项</th>
                                        <th class="sorting text-center">筛选</th>
                                        <th class="sorting text-left">排序</th>
                                        <th class="sorting text-left">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="item in specList">
                                        <td class="text-right">{{item.id}}</td>
                                        <td class="text-left">{{ goodsTypeList[item.type_id]['name'] }}</td>
                                        <td class="text-left">{{item.name}}</td>
                                        <td class="text-left">{{item.spec_item}}</td>
                                        <td class="text-center">
                                            <img @click="changeSearchIndex(item)" width="20" height="20"
                                                 v-bind:src="item.search_index == 1 ? '{$config_siteurl}statics/extres/shop/images/yes.png' : '{$config_siteurl}statics/extres/shop/images/cancel.png'"/>
                                        </td>
                                        <td class="text-right">
                                            <input type="text" class="form-control input-sm"
                                                   @change="updateOrder(item)" size="4" v-model="item.order"/>
                                        </td>
                                        <td class="text-left">
                                            <a href="javascript:" @click="editBtn(item.id)" data-toggle="tooltip"
                                               title="" class="btn btn-primary" data-original-title="编辑"><i
                                                        class="fa fa-pencil"></i></a>
                                            <a @click="delGoodsSpec(item.id)" href="javascript:"
                                               id="button-delete6" data-toggle="tooltip" title="" class="btn btn-danger"
                                               data-original-title="删除"><i class="fa fa-trash-o"></i></a></td>
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
            where: [],
            goodsTypeList: [],
            specList: [],
            page: 1,
            page_count: 1,
        },
        mixins: [window.__baseMethods],
        methods: {
            editBtn: function (id) {
                var that = this
                layer.open({
                    title: '编辑规格',
                    type: 2,
                    area: ['800px', '630px'],
                    fixed: false, //不固定
                    maxmin: false,
                    content: "{:U('Spec/getSpecDetail')}&id=" + id,
                    end: function () {
                        that.getList()
                    }
                });
            },
            addBtn: function () {
                var that = this
                layer.open({
                    title: '编辑规格',
                    type: 2,
                    area: ['800px', '630px'],
                    fixed: false, //不固定
                    maxmin: false,
                    content: "{:U('Spec/getSpecDetail')}",
                    end: function () {
                        that.getList()
                    }
                });
            },
            getList: function () {
                var that = this;
                that.where.page = that.page
                that.httpGet("{:U('Spec/index')}", that.where, function (res) {
                    that.goodsTypeList = res.goodsTypeList;
                    that.specList = res.specList;
                    that.page = res.page['page'];
                    that.page_count = res.page['page_count'];
                    if (that.where.type_id) {
                        setTimeout(function () {
                            $('#type_id').val(that.where.type_id)
                        }, 300)
                    }
                }, false)
            },
            filter: function () {
                var type_id = $('#type_id').val();
                this.where = {'type_id': type_id};
                this.getList();
            },
            changeSearchIndex: function (obj) {
                if (obj.search_index == 1) {
                    obj.search_index = 0;
                } else {
                    obj.search_index = 1;
                }
                var that = this
                that.changeTableVal('shop_spec', 'id', obj.id, 'search_index', obj.search_index)
            },
            updateOrder: function (obj) {
                var that = this
                that.changeTableVal('shop_spec', 'id', obj.id, 'order', obj.order)
            },
            delGoodsSpec: function (id) {
                var that = this
                layer.confirm('确定要删除该规格吗？', {
                    btn: ['确定', '取消']
                }, function () {
                    that.httpPost("{:U('Spec/delGoodsSpec')}", {'id': id}, function (res) {
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
            var that = this
            var where = {}
            if (that.getQueryString('type_id')) {
                var type_id = that.getQueryString('type_id')
                where['type_id'] = type_id
            }
            this.where = where
            this.getList();
        },
        components: {
            'v-page': pageComponent
        }
    });

</script>
</body>
</html>