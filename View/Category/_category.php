<include file="Public/min-header"/>
<div class="wrapper">
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="box" id="app">
                    <div class="box-header">
                        <h3 class="box-title">增加分类</h3>
                    </div>
                    <!-- /.box-header -->
                    <form action="{:U('Category/addEditCategory')}" method="post" class="form-horizontal"
                          id="category_form">
                        <div class="box-body">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">分类名称</label>
                                <div class="col-sm-6">
                                    <input type="text" placeholder="名称" class="form-control large" name="name"
                                           v-model="detail.name">
                                    <span class="help-inline" style="color:#F00; display:none;" id="err_name"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">手机分类名称</label>
                                <div class="col-sm-6">
                                    <input type="text" placeholder="手机分类名称" class="form-control large"
                                           name="mobile_name" v-model="detail.mobile_name">
                                    <span class="help-inline" style="color:#F00; display:none;"
                                          id="err_mobile_name"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">上级分类</label>
                                <div class="col-sm-3">
                                    <select name="parent_cat_id" id="parent_id_1" v-model="pid" class="form-control">
                                        <option value="0">顶级分类</option>
                                        <option v-for="item in catList" v-if="item.parent_id == 0" :value="item.id"
                                                v-bind:selected="item.id == pid ? 'selected' : ''">{{ item.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <select name="cat_id" id="parent_id_2" v-model="detail.parent_id"
                                            class="form-control" style="width:250px;">
                                        <option value="0">顶级分类</option>
                                        <option v-for="item in catList" v-if="item.parent_id == pid" :value="item.id"
                                                v-bind:selected="item.id == detail['parent_id'] ? 'selected' : ''">{{
                                            item.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">导航显示</label>

                                <div class="col-sm-10">
                                    <label>
                                        <input v-model="detail.is_show"
                                               v-bind:checked="detail.is_show == 1 || detail.is_show == '' ? 'checked' : ''"
                                               type="radio" name="is_show" value="1"> 是
                                        <input v-model="detail.is_show"
                                               v-bind:checked="detail.is_show == 0 ? 'checked' : ''" type="radio"
                                               name="is_show" value="0"> 否
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">分类分组:</label>

                                <div class="col-sm-1">
                                    <select name="cat_group" id="cat_group" class="form-control"
                                            v-model="detail.cat_group">
                                        <option value="0">0</option>
                                        <option v-for="item in 20" :value='item'>{{item}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-2">分类展示图片</label>

                                <div class="col-sm-10">
                                    <input class="btn btn-default"
                                           onclick="GetUploadify(1,'image','category','callback');" type="button"
                                           value="上传图片"/>
                                    <input type="text" v-model="detail.image" name="image" id="image"
                                           class="form-control large" style="width:500px;display:initial;"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">显示排序</label>
                                <div class="col-sm-1">
                                    <input type="text" placeholder="50" class="form-control large" name="sort_order"
                                           v-model="detail.sort_order"/>
                                    <span class="help-inline" style="color:#F00; display:none;"
                                          id="err_sort_order"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">分佣比例</label>
                                <div class="col-sm-1">
                                    <input type="text" placeholder="50" class="form-control large"
                                           name="commission_rate" id="commission_rate"
                                           v-model="detail.commission_rate"/>
                                </div>
                                <div class="col-sm-1" style="margin-top: 6px;margin-left: -20px;">
                                    <span>%</span>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <input type="hidden" name="id" value="{$goods_category_info.id}">
                            <button type="reset" class="btn btn-primary pull-left"><i class="icon-ok"></i>重填</button>
                            <button type="button" v-on:click="addEditCate()" class="btn btn-primary pull-right"><i
                                        class="icon-ok"></i>提交
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<include file="Public/vue"/>
<script>
    var obj = new Vue({
        el: '#app',
        data: {
            catList: [],
            detail: [],
            pid: 0
        },
        mixins: [window.__baseMethods],
        methods: {
            getDetail: function () {
                var that = this;
                var id = that.getQueryString('id')
                that.httpGet("{:U('Category/getCategoryDetail')}", {id: id}, function (res) {
                    console.log(res);
                    that.catList = res.cat_list;
                    that.detail = res.goods_category_info;
                    that.pid = res.pid;
                })
            },
            addEditCate: function () {
                var that = this;
                var id = that.getQueryString('id')
                var data = {
                    'detail': that.detail,
                    'pid': that.pid,
                    'id': id
                };
                that.httpPost("{:U('Category/addEditCategory')}", data, function (res) {
                    if (res.status) {
                        layer.alert(res.msg, function () {
                            parent.layer.close(parent.open_window)
                        })
                    } else {
                        layer.alert(res.msg);
                    }
                })
            }
        },
        mounted: function () {
            this.getDetail();
        }
    });

    $('#parent_id_1').change(function () {
        obj.detail.parent_id = 0;
    });

    // 上传商品图片成功回调函数
    function callback(fileurl_tmp) {
        obj.detail['image'] = fileurl_tmp;
    }
</script>
</body>
</html>