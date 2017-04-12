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
              <form action="" id="search-form2" class="navbar-form form-inline" method="post" onsubmit="return false">
                <div class="form-group">
                  <select name="type_id" id="type_id" class="form-control">
                      <option value="">所有分类</option>
                      <option v-for="item in goodsTypeList" :value="item.id">{{item.name}}</option>
                   </select>
                </div>
                <div class="form-group">
	                <button type="submit" v-on:click="filter()" id="button-filter" class="btn btn-primary pull-right">
	                 <i class="fa fa-search"></i> 筛选
	                </button>
                </div> 
                <button type="submit" onclick="location.href='{:U('Spec/getSpecDetail')}'" id="button-filter2" class="btn btn-primary pull-right">
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
                                  <img v-on:click="changeSearchIndex(item)" width="20" height="20" v-bind:src="item.search_index == 1 ? '{$config_siteurl}statics/extres/shop/images/yes.png' : '{$config_siteurl}statics/extres/shop/images/cancel.png'"/>
                              </td>
                              <td class="text-right">
                                  <input type="text" class="form-control input-sm" v-on:change="updateOrder(item)" size="4" v-model="item.order" />
                              </td>
                              <td class="text-left">
                                  <a :href="'{:U('Spec/getSpecDetail')}&id='+item.id" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="编辑"><i class="fa fa-pencil"></i></a>
                                  <a v-on:click="delGoodsSpec(item.id)" href="javascript:;" id="button-delete6" data-toggle="tooltip" title="" class="btn btn-danger" data-original-title="删除"><i class="fa fa-trash-o"></i></a></td>
                          </tr>
                          </tbody>
                      </table>
                  </div>
              </form>
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
            specList: []
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
                        that.goodsTypeList = res.goodsTypeList;
                        that.specList = res.specList;
                    }
                });
            },
            filter: function () {
                var type_id = $('#type_id').val();
                this.where = {'type_id': type_id};
                this.getList();
            },
            changeSearchIndex: function(obj){
                if (obj.search_index == 1){
                    obj.search_index = 0;
                } else {
                    obj.search_index = 1;
                }

                $.ajax({
                    url: "{:U('Shop/AdminApi/changeTableVal')}",
                    data: {
                        'table': 'spec',
                        'id_name': 'id',
                        'id_value': obj.id,
                        'field': 'search_index',
                        'value': obj.search_index
                    },
                    success: function(res){
                        layer.msg('操作成功');
                    }
                });
            },
            updateOrder: function (obj) {
                $.ajax({
                    url: "{:U('Shop/AdminApi/changeTableVal')}",
                    data: {
                        'table': 'spec',
                        'id_name': 'id',
                        'id_value': obj.id,
                        'field': 'order',
                        'value': obj.order
                    },
                    success: function(res){
                        layer.msg('操作成功');
                    }
                });
            },
            delGoodsSpec: function (id) {
                layer.confirm('确定要删除该规格吗？',{
                    btn:['确定', '取消']
                },function () {
                    $.ajax({url: "{:U('Spec/delGoodsSpec')}", type: 'get', data: {'id': id}, dataType: 'json',
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