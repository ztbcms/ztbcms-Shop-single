<include file="Public/min-header"/>
<style>
    #search-form > .form-group {
        margin-left: 10px;
    }
</style>
<div class="wrapper" id="app">
    <include file="Public/breadcrumb"/>
    <section class="content">
        <section class="content-header">
            <h1>
                首页推荐
            </h1>
            <span style="position: absolute;top: 27%;right: 0%;">
                <a class="btn btn-primary" onclick="$('#addRecomModal').modal()" href="javascript:">
                    添加推荐位
                </a>
            </span>
        </section>
        <section style="margin-top: 20px;">
            <div v-for="item in recoms" class="box">
                <div class="box-header">
                    <h3 class="box-title">{{ item.name }}</h3>
                    <span style="float: right;">
                        <a class="btn btn-primary" @click="addRecomItemModelBtn(item.id)" href="javascript:">添加推荐内容</a>
                        <a class="btn btn-danger" @click="delBtn(item)" href="javascript:">删除</a>
                    </span>
                </div>
                <div class="box-body">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid"
                                       aria-describedby="example2_info">
                                    <thead>
                                    <tr role="row">
                                        <th>
                                            排序
                                        </th>
                                        <th>名称
                                        </th>
                                        <th>
                                            图片
                                        </th>
                                        <th>
                                            跳转链接
                                        </th>
                                        <th>
                                            操作
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="i in item.item_list" role="row" class="odd">
                                        <td>
                                            {{i.sort}}
                                        </td>
                                        <td class="sorting_1">{{ i.name }}</td>
                                        <td>
                                            <img style="width:200px;" :src="i.img_url" alt="">
                                        </td>
                                        <td>{{ i.link }}</td>
                                        <td style="width: 150px;">
                                            <a class="btn btn-primary" @click="editRecomItemModalBtn(i)"
                                               href="javascript:">编辑</a>
                                            <a class="btn btn-danger" @click="delRecomItem(i)"
                                               href="javascript:">删除</a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
    <!-- 编辑推荐内容modal-->
    <div id="editRecomItemModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">编辑推荐内容</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="">推荐内容标题</label>
                        </div>
                        <div class="col-md-9">
                            <input v-model="editRecomItem.name" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="">所属推荐位</label>
                        </div>
                        <div class="col-md-9">
                            <select class="form-control" name="" id="edit_recom_id">
                                <option v-for="item in recoms" :value="item.id">
                                    {{item.name}}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="">推荐链接</label>
                        </div>
                        <div class="col-md-9">
                            <img style="width: 200px;" id="edit_show_img" src="" alt="">
                            <input type="hidden" id="edit_img_url">
                            <input type="button" class="btn btn-default" value="上传图片"
                                   onclick="GetUploadify(1,'','recom','edit_call_back');"/>
                            <p>*请保持同一个推荐位的图片尺寸一致</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="">推荐链接</label>
                        </div>
                        <div class="col-md-9">
                            <input v-model="editRecomItem.link" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="">排序</label>
                        </div>
                        <div class="col-md-9">
                            <input v-model="editRecomItem.sort" type="number" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="">状态</label>
                        </div>
                        <div class="col-md-9">
                            <select v-model="editRecomItem.status" class="form-control">
                                <option value="1">显示</option>
                                <option value="0">不显示</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" @click="editRecomItemBtn">保存</button>
                </div>
            </div>
        </div>
    </div>
    <!--  /编辑推荐内容modal  -->
    <!-- 添加推荐内容modal-->
    <div id="addRecomItemModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">添加推荐内容</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="">推荐内容标题</label>
                        </div>
                        <div class="col-md-9">
                            <input v-model="addRecomItem.name" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="">所属推荐位</label>
                        </div>
                        <div class="col-md-9">
                            <select class="form-control" name="" id="recom_id">
                                <option v-for="item in recoms" :value="item.id">
                                    {{item.name}}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="">推荐链接</label>
                        </div>
                        <div class="col-md-9">
                            <img id="show_img" style="width: 200px;" src="" alt="">
                            <input type="hidden" id="img_url">
                            <input type="button" class="btn btn-default" value="上传图片"
                                   onclick="GetUploadify(1,'','recom','call_back');"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="">推荐链接</label>
                        </div>
                        <div class="col-md-9">
                            <input v-model="addRecomItem.link" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="">排序</label>
                        </div>
                        <div class="col-md-9">
                            <input v-model="addRecomItem.sort" type="number" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="">状态</label>
                        </div>
                        <div class="col-md-9">
                            <select v-model="addRecomItem.status" class="form-control">
                                <option value="1">显示</option>
                                <option value="0">不显示</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" @click="addRecomItemBtn"> 添加</button>
                </div>
            </div>
        </div>
    </div>
    <!--  /添加推荐内容modal  -->
    <!-- 添加推荐位modal-->
    <div id="addRecomModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">添加推荐位</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="">推荐位名称</label>
                        </div>
                        <div class="col-md-9">
                            <input v-model="addRecom.name" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="">排序</label>
                        </div>
                        <div class="col-md-9">
                            <input v-model="addRecom.sort" type="number" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="">状态</label>
                        </div>
                        <div class="col-md-9">
                            <select v-model="addRecom.status" class="form-control">
                                <option value="1">显示</option>
                                <option value="0">不显示</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" @click="addRecomBtn"> 添加</button>
                </div>
            </div>
        </div>
    </div>
    <!--  /添加推荐位modal  -->
</div>
<include file="Public/vue"/>
<script>
    function call_back(event) {
//        var event = JSON.parse(event)
        console.log(event)
        $('#show_img').attr('src', event)
        $('#img_url').val(event)
    }
    function edit_call_back(event) {
//        var event = JSON.parse(event)
        console.log(event)
        $('#edit_show_img').attr('src', event)
        $('#edit_img_url').val(event)
    }
    $(document).ready(function () {
        new Vue({
            el: '#app',
            data: {
                addRecom: {
                    sort: 100,
                    status: 1,
                },
                recoms: [],
                addRecomItem: {
                    status: 1
                },
                editRecomItem: {}
            },
            methods: {
                delRecomItem: function (item) {
                    console.log(item)
                    var that = this
                    if (confirm('是否确定删除')) {
                        $.ajax({
                            url: '{:U("Shop/Shop/delRecomItem")}',
                            data: {id: item.id},
                            type: 'post',
                            dataType: 'json',
                            success: function (res) {
                                if (res.status) {
                                    layer.msg('删除成功')
                                    setTimeout(function () {
                                        location.reload()
                                    }, 1000)
                                } else {
                                    layer.msg(res.info)
                                }
                            }
                        })
                    }
                },
                editRecomItemBtn: function () {
                    var data = {
                        id: this.editRecomItem.id,
                        name: this.editRecomItem.name,
                        recom_id: $('#edit_recom_id').val(),
                        link: this.editRecomItem.link,
                        img_url: $('#edit_img_url').val(),
                        sort: this.editRecomItem.sort,
                        status: this.editRecomItem.status
                    }
                    console.log(data)
                    $.ajax({
                        url: '{:U("Shop/Shop/addRecomItem")}',
                        data: data,
                        type: 'post',
                        dataType: 'json',
                        success: function (res) {
                            if (res.status) {
                                layer.msg('更新成功')
                                setTimeout(function () {
                                    location.reload()
                                }, 1000)
                            } else {
                                layer.msg(res.info)
                            }
                        }
                    })
                },
                editRecomItemModalBtn: function (item) {
                    this.editRecomItem = item
                    console.log(this.editRecomItem)
                    $('#edit_recom_id').val(this.editRecomItem.recom_id)
                    $('#edit_img_url').val(this.editRecomItem.img_url)
                    $('#edit_show_img').attr('src', this.editRecomItem.img_url)
                    $('#editRecomItemModal').modal()
                },
                addRecomItemBtn: function () {
                    var data = {
                        name: this.addRecomItem.name,
                        recom_id: $('#recom_id').val(),
                        link: this.addRecomItem.link,
                        img_url: $('#img_url').val(),
                        sort: this.addRecomItem.sort,
                        status: this.addRecomItem.status
                    }
                    console.log(data)
                    $.ajax({
                        url: '{:U("Shop/Shop/addRecomItem")}',
                        data: data,
                        type: 'post',
                        dataType: 'json',
                        success: function (res) {
                            if (res.status) {
                                layer.msg('添加成功')
                                setTimeout(function () {
                                    location.reload()
                                }, 1000)
                            } else {
                                layer.msg(res.info)
                            }
                        }
                    })
                },
                addRecomItemModelBtn: function (recom_id) {
                    $('#recom_id').val(recom_id)
                    this.addRecomItem.recom_id
                    $('#addRecomItemModal').modal()
                },
                delBtn: function (item) {
                    var that = this
                    if (confirm('是否确定删除?')) {
                        console.log('确定删除')
                        $.ajax({
                            url: '{:U("Shop/Shop/delRecom")}',
                            data: item,
                            type: 'post',
                            dataType: 'json',
                            success: function (res) {
                                if (res.status) {
                                    layer.msg('删除成功')
                                    setTimeout(function () {
                                        location.reload()
                                    }, 1000)
                                } else {
                                    layer.msg(res.info)
                                }
                            }
                        })
                    }
                },
                addRecomBtn: function () {
                    var data = {
                        name: this.addRecom.name,
                        sort: this.addRecom.sort,
                        status: this.addRecom.status
                    }
                    $.ajax({
                        url: '{:U("Shop/Shop/addRecom")}',
                        data: data,
                        type: 'post',
                        dataType: 'json',
                        success: function (res) {
                            if (res.status) {
                                layer.msg('添加成功')
                                setTimeout(function () {
                                    location.reload()
                                }, 1000)
                            } else {
                                layer.msg(res.info)
                            }
                        }
                    })
                    console.log(data)
                }
            },
            mounted: function () {
                var that = this
                $.ajax({
                    url: '{:U("Shop/Shop/recom")}',
                    data: {},
                    type: 'get',
                    dataType: 'json',
                    success: function (res) {
                        console.log(res)
                        that.recoms = res.info
                    }
                })
            }
        })
    });
</script>
</body>

</html>