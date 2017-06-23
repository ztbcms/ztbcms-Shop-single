<include file="Public/min-header"/>
<div class="wrapper">
    <include file="Public/breadcrumb"/>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box" id="app">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-1" style="display: inline-block;">
                                <button class="btn btn-default" type="button" onclick="tree_open(this);"><i
                                            class="fa fa-angle-double-down"></i>展开
                                </button>
                            </div>
                            <div class="col-md-2 pull-right">
                                <a href="javascript:;" @click="addBtn" class="btn btn-primary "><i
                                            class="fa fa-plus"></i>新增分类</a>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="list-table" class="table table-bordered table-striped dataTable" role="grid"
                                       aria-describedby="example1_info">
                                    <thead>
                                    <tr role="row">
                                        <th align="left">分类ID</th>
                                        <th align="left">分类名称</th>
                                        <th align="left">手机显示名称</th>
                                        <th align="left">是否推荐</th>
                                        <th align="left">是否显示</th>
                                        <th align="left">分组</th>
                                        <th align="left">排序</th>
                                        <th align="left">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="item in catList" role="row" align="left" :class="item.level"
                                        :id="item.level+'_'+item.id"
                                        v-bind:style="item.level > 1 ? 'display:none' : ''">
                                        <td align="left">{{item.id}}</td>
                                        <td :style="'padding-left:'+item.level*5+'em'">
                                            <span v-if="item.have_son == 1" class="glyphicon glyphicon-plus btn-warning"
                                                  style="padding:2px; font-size:12px;"
                                                  :id="'icon_'+item.level+'_'+item.id" aria-hidden="false"
                                                  onclick="rowClicked(this)"></span>&nbsp;
                                            <span>{{item.name}}</span>
                                        </td>
                                        <td align="left"><span>{{item.mobile_name}}</span></td>
                                        <td align="left">
                                            <img v-on:click="change(item,'is_hot')" width="20" height="20"
                                                 v-bind:src="item.is_hot == 1 ? '{$config_siteurl}statics/extres/shop/images/yes.png' : '{$config_siteurl}statics/extres/shop/images/cancel.png'"/>
                                        </td>
                                        <td align="left">
                                            <img v-on:click="change(item,'is_show')" width="20" height="20"
                                                 v-bind:src="item.is_show == 1 ? '{$config_siteurl}statics/extres/shop/images/yes.png' : '{$config_siteurl}statics/extres/shop/images/cancel.png'"/>
                                        </td>
                                        <td align="left">
                                            <input type="text" class="form-control input-sm"
                                                   v-on:change="update(item,'cat_group')" size="4"
                                                   v-model="item.cat_group"/>
                                        </td>
                                        <td align="left">
                                            <input type="text" class="form-control input-sm"
                                                   v-on:change="update(item,'sort_order')" size="4"
                                                   v-model="item.sort_order"/>
                                        </td>
                                        <td align="left">
                                            <a class="btn btn-primary" @click="editBtn(item.id)" href="javascript:;"><i
                                                        class="fa fa-pencil"></i></a>
                                            <a class="btn btn-danger" href="javascript:;"
                                               v-on:click="delGoodsCategory(item.id)"><i class="fa fa-trash-o"></i></a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5">
                                <!--<div class="dataTables_info" id="example1_info" role="status" aria-live="polite">分页</div>-->
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </section>
</div>
<include file="Public/vue"/>
<script>
    var open_window = null;
    new Vue({
        el: '#app',
        data: {
            where: {},
            catList: []
        },
        mixins: [window.__baseMethods],
        methods: {
            editBtn: function (id) {
                var that = this
                open_window = layer.open({
                    title: '添加分类',
                    type: 2,
                    area: ['800px', '630px'],
                    fixed: false, //不固定
                    maxmin: false,
                    content: "{:U('Category/getCategoryDetail')}&id=" + id,
                    end: function () {
                        that.getList()
                    }
                });
            },
            addBtn: function () {
                var that = this
                open_window = layer.open({
                    title: '添加分类',
                    type: 2,
                    area: ['800px', '630px'],
                    fixed: false, //不固定
                    maxmin: false,
                    content: "{:U('Category/getCategoryDetail')}",
                    end: function () {
                        that.getList()
                    }
                });
            },
            getList: function () {
                var that = this;
                that.httpGet("{:U('Category/index')}", that.where, function (res) {
                    console.log(res);
                    that.catList = res.cat_list;
                }, false)
            },
            change: function (obj, field) {
                if (obj[field] == 1) {
                    obj[field] = 0;
                } else {
                    obj[field] = 1;
                }

                this.update(obj, field);
            },
            update: function (obj, field) {
                console.log(obj);
                var that = this
                that.changeTableVal('ShopGoodsCategory', 'id', obj.id, field, obj[field])
            },
            delGoodsCategory: function (id) {
                var that = this;
                layer.confirm('确定要删除该分类吗？', {
                    btn: ['确定', '取消']
                }, function () {
                    that.httpPost("{:U('Category/delGoodsCategory')}", {'id': id}, function (res) {
                        layer.alert(res.info, {
                            icon: res.status
                        }, function () {
                            layer.closeAll();
                            that.getList();
                        });
                    })
                });
            }
        },
        mounted: function () {
            this.getList();
        }
    });
</script>
<script type="text/javascript">
    // 展开收缩
    function tree_open(obj) {
        var tree = $('#list-table tr[id^="2_"], #list-table tr[id^="3_"] '); //,'table-row'
        if (tree.css('display') == 'table-row') {
            $(obj).html("<i class='fa fa-angle-double-down'></i>展开");
            tree.css('display', 'none');
            $("span[id^='icon_']").removeClass('glyphicon-minus');
            $("span[id^='icon_']").addClass('glyphicon-plus');
        } else {
            $(obj).html("<i class='fa fa-angle-double-up'></i>收缩");
            tree.css('display', 'table-row');
            $("span[id^='icon_']").addClass('glyphicon-minus');
            $("span[id^='icon_']").removeClass('glyphicon-plus');
        }
    }

    // 以下是 bootstrap 自带的  js
    function rowClicked(obj) {
        span = obj;

        obj = obj.parentNode.parentNode;

        var tbl = document.getElementById("list-table");

        var lvl = parseInt(obj.className);

        var fnd = false;

        var sub_display = $(span).hasClass('glyphicon-minus') ? 'none' : '' ? 'block' : 'table-row';
        //console.log(sub_display);
        if (sub_display == 'none') {
            $(span).removeClass('glyphicon-minus btn-info');
            $(span).addClass('glyphicon-plus btn-warning');
        } else {
            $(span).removeClass('glyphicon-plus btn-info');
            $(span).addClass('glyphicon-minus btn-warning');
        }

        for (i = 0; i < tbl.rows.length; i++) {
            var row = tbl.rows[i];

            if (row == obj) {
                fnd = true;
            } else {
                if (fnd == true) {
                    var cur = parseInt(row.className);
                    var icon = 'icon_' + row.id;
                    if (cur > lvl) {
                        row.style.display = sub_display;
                        if (sub_display != 'none') {
                            var iconimg = document.getElementById(icon);
                            $(iconimg).removeClass('glyphicon-plus btn-info');
                            $(iconimg).addClass('glyphicon-minus btn-warning');
                        } else {
                            $(iconimg).removeClass('glyphicon-minus btn-info');
                            $(iconimg).addClass('glyphicon-plus btn-warning');
                        }
                    } else {
                        fnd = false;
                        break;
                    }
                }
            }
        }

        for (i = 0; i < obj.cells[0].childNodes.length; i++) {
            var imgObj = obj.cells[0].childNodes[i];
            if (imgObj.tagName == "IMG") {
                if ($(imgObj).hasClass('glyphicon-plus btn-info')) {
                    $(imgObj).removeClass('glyphicon-plus btn-info');
                    $(imgObj).addClass('glyphicon-minus btn-warning');
                } else {
                    $(imgObj).removeClass('glyphicon-minus btn-warning');
                    $(imgObj).addClass('glyphicon-plus btn-info');
                }
            }
        }

    }
</script>
</body>

</html>