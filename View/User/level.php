<include file="Public/min-header"/>
<div class="wrapper">
    <include file="Public/breadcrumb"/>
    <!-- Main content -->
    <section class="content" id="app">
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i>会员等级</h3>
                </div>
                <div class="panel-body">
                    <div class="navbar navbar-default">
                        <div class="navbar-form form-inline">
                            <button @click="addBtnModdal" class="btn btn-primary pull-right" href="javascript:">新增等级
                            </button>
                        </div>
                    </div>
                    <div>
                        <form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <td>
                                            <a href="javascript:">ID</a>
                                        </td>
                                        <td>
                                            <a href="javascript:">名称</a>
                                        </td>
                                        <td>
                                            <a href="javascript:">满足金额/积分</a>
                                        </td>
                                        <td>
                                            <a href="javascript:">折扣</a>
                                        </td>
                                        <td>
                                            <a href="javascript:">介绍</a>
                                        </td>
                                        <td>操作</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="item in lists">
                                        <td>
                                            {{ item.level_id }}
                                        </td>
                                        <td>
                                            {{ item.level_name }}
                                        </td>
                                        <td>
                                            {{ item.amount ? item.amount:0 }}
                                        </td>
                                        <td>
                                            {{ item.discount ? item.discount:1 }}
                                        </td>
                                        <td>
                                            {{ item.description }}
                                        </td>
                                        <td>
                                            <a @click="editBtnModel(item)" class="btn btn-info"
                                               data-original-title="查看详情">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a @click="deleteBtn(item.level_id)" class="btn btn-danger"
                                               data-original-title="删除">
                                                <i class="fa fa-trash-o"></i>
                                            </a>
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
        </div>
        <div id="editModal" class="modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                        <h4 class="modal-title">编辑等级</h4>
                    </div>
                    <div class="modal-body">
                        <form id="editForm">
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <p style="text-align: right">名称</p>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" v-model="editData.level_name" name="level_name"
                                           class="form-control" placeholder="请输入名称">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <p style="text-align: right">满足金额/积分</p>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" v-model="editData.amount" name="amount" class="form-control"
                                           placeholder="请输入满足等级的金额（可选）">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <p style="text-align: right">折扣</p>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" v-model="editData.discount" name="discount" class="form-control"
                                           placeholder="请输入会员享受的等级折扣 0~1 ">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <p style="text-align: right">介绍</p>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" v-model="editData.description" name="description"
                                           class="form-control" placeholder="请输入介绍">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">关闭</button>
                        <button type="button" @click="editBtn" class="btn btn-primary">保存</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="addModal" class="modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                        <h4 class="modal-title">添加等级</h4>
                    </div>
                    <div class="modal-body">
                        <form id="addForm">
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <p style="text-align: right">名称</p>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" v-model="addData.level_name" name="level_name"
                                           class="form-control" placeholder="请输入名称">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <p style="text-align: right">满足金额/积分</p>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" v-model="addData.amount" name="amount" class="form-control"
                                           placeholder="请输入满足等级的金额（可选）">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <p style="text-align: right">折扣</p>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" v-model="addData.discount" name="discount" class="form-control"
                                           placeholder="请输入会员享受的等级折扣 0~1 ">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <p style="text-align: right">介绍</p>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" v-model="addData.description" name="description"
                                           class="form-control" placeholder="请输入介绍">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">关闭</button>
                        <button type="button" @click="addBtn" class="btn btn-primary">添加</button>
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
                total: 0,
                page_count: 0,
                addData: {},
                editData: {}
            },
            mixins: [window.__baseMethods, window.__baseFilters],
            methods: {
                deleteBtn: function (level_id) {
                    var that = this
                    layer.confirm('是否确认删除？', function () {
                        that.httpPost('{:U("Shop/User/deleteLevel")}', {level_id: level_id}, function (res) {
                            if (res.status) {
                                layer.msg('删除成功', function () {
                                    that.getList()
                                })
                            } else {
                                layer.msg(res.info)
                            }
                        })
                    })
                },
                addBtn: function () {
                    var that = this
                    var params = $('#addForm').serialize()
                    if (!this.addData.level_name) {
                        layer.msg('请输入等级名称')
                        return;
                    }
                    that.httpPost('{:U("Shop/User/addLevel")}', params, function (res) {
                        if (res.status) {
                            layer.msg('添加成功', function () {
                                that.getList()
                                $('#addModal').modal('hide')
                            })
                        } else {
                            layer.msg(res.info)
                        }
                    })
                    console.log(this.addData)
                },
                addBtnModdal: function () {
                    $('#addModal').modal()
                },
                editBtn: function () {
                    var that = this
                    var params = $('#editForm').serialize()
                    params += '&level_id=' + that.editData.level_id
                    if (!this.editData.level_name) {
                        layer.msg('请输入等级名称')
                        return;
                    }
                    that.httpPost('{:U("Shop/User/editLevel")}', params, function (res) {
                        if (res.status) {
                            layer.msg('修改成功', function () {
                                that.getList()
                                $('#editModal').modal('hide')
                            })
                        } else {
                            layer.msg(res.info)
                        }
                    })
                },
                editBtnModel: function (item) {
                    this.editData = item
                    $('#editModal').modal()
                },
                getList: function () {
                    var that = this
                    var where = {page: that.page, limit: that.limit}
                    that.httpGet('{:U("Shop/User/ajaxLevel")}', where, function (res) {
                        console.log(res)
                        var data = res.info
                        that.lists = data.lists
                        that.page = data.page
                        that.limit = data.limit
                        that.total = data.total
                        that.page_count = data.page_count
                    })
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