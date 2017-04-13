<include file="Public/min-header"/>
<div class="wrapper">
    <include file="Public/breadcrumb"/>
    <section class="content">
        <div class="row">
           <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                    	<i class="fa fa-list"></i>&nbsp;商品咨询列表
                    </h3>
                </div>
                <div class="panel-body" id="app">
                <nav class="navbar navbar-default">	     
			        <div class="collapse navbar-collapse">
			          <form action="" id="search-form2" class="navbar-form form-inline" role="search" method="post" onsubmit="return false;">
                          <div class="form-group">
                              <input type="text" class="form-control" v-model="where.username" name="nickname" placeholder="搜索用户">
                          </div>
                          <div class="form-group">
                              <input type="text" class="form-control" v-model="where.content" name="content" placeholder="搜索评论内容">
                          </div>
                          <button type="button" v-on:click="filter()" class="btn btn-info"><i class="fa fa-search"></i> 筛选</button>
			          </form>		
			      </div>
    			</nav>
                    <div id="ajax_return">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <td class="text-center">
                                        用户
                                    </td>
                                    <td class="text-center">
                                        咨询类型
                                    </td>
                                    <td class="text-center">
                                        咨询内容
                                    </td>
                                    <td class="text-center">
                                        商品
                                    </td>
                                    <td class="text-center">
                                        显示
                                    </td>
                                    <td class="text-center">
                                        咨询时间
                                    </td>
                                    <td class="text-center">操作</td>
                                </tr>
                                </thead>
                                <tbody>

                                    <tr v-for="item in commentList">
                                        <td class="text-center">{{item.username}}</td>
                                        <td class="text-center">{{ consultType[item.consult_type] }}</td>
                                        <td class="text-center">{{item.content}}</td>
                                        <td class="text-center">
                                            <a>{{ goodsList[item.goods_id] }}</a>
                                        </td>
                                        <td class="text-center">
                                            <img v-on:click="changeShow(item)" width="20" height="20" v-bind:src="item.is_show == 1 ? '{$config_siteurl}statics/extres/shop/images/yes.png' : '{$config_siteurl}statics/extres/shop/images/cancel.png'"/>
                                        </td>
                                        <td class="text-center">{{item.add_time | getFormatTime}}</td>
                                        <td class="text-center">
                                            <a :href="'{:U('Comment/consult_info')}&id='+item.id" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="编辑"><i class="fa fa-eye"></i></a>
                                            <a href="javascript:void(0);" v-on:click="delAsk(item.id)" id="button-delete6" data-toggle="tooltip" title="" class="btn btn-danger" data-original-title="删除"><i class="fa fa-trash-o"></i></a>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
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
            consultType: [],
            goodsList: [],
            commentList: []
        },
        filters: {
            getFormatTime: function (value) {
                var time = new Date(parseInt(value * 1000));
                var y = time.getFullYear();
                var m = time.getMonth() + 1;
                var d = time.getDate();
                var h = time.getHours();
                var i = time.getMinutes();
                var res = y + '-' + (m < 10 ? '0' + m : m) + '-' + (d < 10 ? '0' + d : d)
                res += '  ' + (h < 10 ? '0' + h : h) + ':' + (i < 10 ? '0' + i : i);
                return res;
            }
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
                        that.consultType = res.consult_type;
                        that.goodsList = res.goods_list;
                        that.commentList = res.comment_list;
                    }
                });
            },
            filter: function () {
                this.getList();
            },
            changeShow: function(obj){
                if (obj.is_show == 1){
                    obj.is_show = 0;
                } else {
                    obj.is_show = 1;
                }

                $.ajax({
                    url: "{:U('Shop/AdminApi/changeTableVal')}",
                    data: {
                        'table': 'goods_consult',
                        'id_name': 'id',
                        'id_value': obj.id,
                        'field': 'is_show',
                        'value': obj.is_show
                    },
                    success: function(res){
                        layer.msg('操作成功');
                    }
                });
            },
            delAsk : function (id) {
                layer.confirm('确定要删除该咨询吗？',{
                    btn:['确定', '取消']
                },function () {
                    $.ajax({url: "{:U('Comment/delAsk')}", type: 'get', data: {'id': id}, dataType: 'json',
                        success:function (res){
                            layer.alert(res.info,{
                                icon: res.status
                            },function(){
                                window.location.reload();
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

</body>
</html>