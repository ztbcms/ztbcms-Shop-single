<include file="Public/min-header"/>
<div class="wrapper">
    <include file="Public/breadcrumb"/>
    <!-- Main content -->
    <section class="content" id="app">
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 商品类型列表</h3>
                </div>
                <div class="panel-body">
                    <div class="navbar navbar-default">
                        <div class="row navbar-form">
                            <button type="submit" @click="addBtn"
                                    class="btn btn-primary pull-right"><i class="fa fa-plus"></i>新增商品类型
                            </button>
                        </div>
                    </div>
                    <div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="sorting text-center">ID</th>
                                    <th class="sorting text-center">类型名</th>
                                    <th class="sorting text-center">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in lists">
                                    <td class="text-center">{{ item.id }}</td>
                                    <td class="text-center">{{ item.name }}</td>
                                    <td class="text-center">
                                        <a :href="'{:U('Spec/index')}&type_id='+item.id" data-toggle="tooltip" title=""
                                           class="btn btn-info" data-original-title="属性列表">规格</a>
                                        <a :href="'{:U('Attribute/index')}&type_id='+item.id" data-toggle="tooltip" title=""
                                           class="btn btn-info" data-original-title="属性列表">属性</a>
                                        <a href="javascript:" @click="editBtn(item.id)" data-toggle="tooltip"
                                           title="" class="btn btn-primary" data-original-title="编辑"><i
                                                    class="fa fa-pencil"></i></a>
                                        <a href="javascript:" @click="delGoodsType(item.id)" data-toggle="tooltip"
                                           title="" class="btn btn-danger" data-original-title="删除"><i
                                                    class="fa fa-trash-o"></i></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
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
            lists: [],
            page: 1,
            page_count: 1,
        },
        mixins: [window.__baseMethods],
        methods: {
            editBtn: function (id) {
                var that = this
                open_window = layer.open({
                    title: '编辑类型',
                    type: 2,
                    area: ['800px', '330px'],
                    fixed: false, //不固定
                    maxmin: false,
                    content: "{:U('Type/getTypeDetail')}&id=" + id,
                    end: function () {
                        that.getList()
                    }
                });
            },
            addBtn: function () {
                var that = this
                open_window = layer.open({
                    title: '添加类型',
                    type: 2,
                    area: ['800px', '330px'],
                    fixed: false, //不固定
                    maxmin: false,
                    content: "{:U('Type/getTypeDetail')}",
                    end: function () {
                        that.getList()
                    }
                });
            },
            getList: function () {
                var that = this;
                var where = {'page': that.page}
                that.httpGet("{:U('Type/index')}", where, function (res) {
                    if (res.status) {
                        that.lists = res.data;
                        that.page = res.page['page'];
                        that.page_count = res.page['page_count'];
                    }
                }, false)
            },
            delGoodsType: function (id) {
                var that = this
                layer.confirm('确定要删除该分类吗？', {
                    btn: ['确定', '取消']
                }, function () {
                    that.httpPost("{:U('Type/delGoodsType')}", {'id': id}, function (res) {
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
                this.page = page;
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