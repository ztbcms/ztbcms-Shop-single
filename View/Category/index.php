<include file="Public/min-header" />
<div class="wrapper">
    <include file="Public/breadcrumb" />
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box" id="app">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-1" style="display: inline-block;">
                                <button class="btn btn-default" type="button" onclick="tree_open(this);"><i class="fa fa-angle-double-down"></i>展开</button>
                            </div>
                            <div class="col-md-2 pull-right">
                                <a href="{:U('Category/getCategoryDetail')}" class="btn btn-primary "><i class="fa fa-plus"></i>新增分类</a>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="list-table" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                                    <thead>
                                        <tr role="row">
                                            <th valign="middle">分类ID</th>
                                            <th valign="middle">分类名称</th>
                                            <th valign="middle">手机显示名称</th>
                                            <th valign="middle">是否推荐</th>
                                            <th valign="middle">是否显示</th>
                                            <th valign="middle">分组</th>
                                            <th valign="middle">排序</th>
                                            <th valign="middle">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="item in catList" role="row" align="center" :class="item.level" :id="item.level+'_'+item.id" v-bind:style="item.level > 1 ? 'display:none' : ''">
                                    <td>{{item.id}}</td>
                                    <td align="left" :style="'padding-left:'+item.level*5+'em'">
                                        <span v-if="item.have_son == 1" class="glyphicon glyphicon-plus btn-warning" style="padding:2px; font-size:12px;" :id="'icon_'+item.level+'_'+item.id" aria-hidden="false" onclick="rowClicked(this)"></span>&nbsp;
                                        <span>{{item.name}}</span>
                                    </td>
                                    <td><span>{{item.mobile_name}}</span></td>
                                    <td>
                                        <img v-on:click="change(item,'is_hot')" width="20" height="20" v-bind:src="item.is_hot == 1 ? '{$config_siteurl}statics/extres/shop/images/yes.png' : '{$config_siteurl}statics/extres/shop/images/cancel.png'"/>
                                    </td>
                                    <td>
                                        <img v-on:click="change(item,'is_show')" width="20" height="20" v-bind:src="item.is_show == 1 ? '{$config_siteurl}statics/extres/shop/images/yes.png' : '{$config_siteurl}statics/extres/shop/images/cancel.png'"/>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control input-sm" v-on:change="update(item,'cat_group')" size="4" v-model="item.cat_group" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control input-sm" v-on:change="update(item,'sort_order')" size="4" v-model="item.sort_order" />
                                    </td>
                                    <td>
                                        <a class="btn btn-primary" :href="'{:U('Category/getCategoryDetail')}&id='+item.id"><i class="fa fa-pencil"></i></a>
                                        <a class="btn btn-danger" href="javascript:;" v-on:click="delGoodsCategory(item.id)"><i class="fa fa-trash-o"></i></a>
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
    new Vue({
        el: '#app',
        data: {
            where: {},
            catList: []
        },
        methods: {
            getList: function(){
                var that = this;
                $.ajax({
                    url: '',
                    type: 'post',
                    data: that.where,
                    dataType: 'json',
                    success: function(res){
                        console.log(res);
                        that.catList = res.cat_list;
                    }
                });
            },
            change: function(obj,field){
                if (obj[field] == 1){
                    obj[field] = 0;
                } else {
                    obj[field] = 1;
                }

                this.update(obj,field);
            },
            update: function(obj,field){
                console.log(obj);
                $.ajax({
                    url: "{:U('Shop/AdminApi/changeTableVal')}",
                    data: {
                        'table': 'goodsCategory',
                        'id_name': 'id',
                        'id_value': obj.id,
                        'field': field,
                        'value': obj[field]
                    },
                    success: function(res){
                        layer.msg('操作成功');
                    }
                });
            },
            delGoodsCategory: function(id){
                var that = this;
                layer.confirm('确定要删除该分类吗？',{
                    btn:['确定', '取消']
                },function () {
                    $.ajax({url: "{:U('Category/delGoodsCategory')}", type: 'get', data: {'id': id}, dataType: 'json',
                        success:function (res){
                            layer.alert(res.info,{
                                icon: res.status
                            },function(){
                                layer.closeAll();
                                that.getList();
                            });
                        }
                    });
                });
            }
        },
        mounted: function(){
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