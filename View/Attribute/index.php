<include file="Public/min-header"/>
<div class="wrapper">
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="fa fa-list"></i> 商品属性</h3>
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
                  <button type="button" onclick="location.href='{:U('Attribute/getAttrDetail')}'" class="btn btn-primary pull-right">
                 <i class="fa fa-plus"></i> 添加属性
                </button>
              </form>
          </div>
          <div>
              <form method="post" enctype="multipart/form-data" target="_blank" id="form-goodsType">
                  <div class="table-responsive">
                      <table class="table table-bordered table-hover">
                          <thead>
                          <tr>
                              <th class="sorting text-left">ID</th>
                              <th class="sorting text-left">属性名称</th>
                              <th class="sorting text-left">商品类型</th>
                              <th class="sorting text-left">属性值的输入方式</th>
                              <th class="sorting text-left">可选值列表</th>
                              <th class="sorting text-center">筛选</th>
                              <th class="sorting text-left">排序</th>
                              <th class="sorting text-right">操作</th>
                          </tr>
                          </thead>
                          <tbody>
                              <tr v-for="item in goodsAttributeList">
                                  <td class="text-right">{{item.attr_id}}</td>
                                  <td class="text-left">{{item.attr_name}}</td>
                                  <td class="text-left">{{ goodsTypeList[item.type_id]['name'] }}</td>
                                  <td class="text-left">{{ attr_input_type[item.attr_input_type] }}</td>
                                  <td class="text-left">{{item.attr_values}}</td>
                                  <td class="text-center">
                                      <img v-on:click="changeAttrIndex(item)" width="20" height="20" v-bind:src="item.attr_index == 1 ? '{$config_siteurl}statics/extres/shop/images/yes.png' : '{$config_siteurl}statics/extres/shop/images/cancel.png'"/>
                                  </td>
                                  <td class="text-left">
                                      <input type="text" class="form-control input-sm" v-on:change="updateOrder(item)" size="4" v-model="item.order" />
                                  </td>
                                  <td class="text-right">
                                      <a :href="'{:U('Attribute/getAttrDetail')}&id='+item.attr_id" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="编辑"><i class="fa fa-pencil"></i></a>
                                      <a v-on:click="delGoodsAttr(item.attr_id)" href="javascript:;" id="button-delete6" data-toggle="tooltip" title="" class="btn btn-danger" data-original-title="删除"><i class="fa fa-trash-o"></i></a></td>
                              </tr>
                          </tbody>
                      </table>
                  </div>
              </form>
              <div class="dataTables_paginate paging_simple_numbers">
                  <button v-on:click="toPage( parseInt(page) - 1 )" class="btn btn-primary">上一页
                  </button>
                  <button v-on:click="toPage( parseInt(page) + 1 )" class="btn btn-primary">下一页
                  </button>
                  <span style="line-height: 30px;margin-left: 50px"><input id="ipt_page"
                                                                           style="width:30px;"
                                                                           type="text"
                                                                           v-model="temp_page"> / {{ page_count }}</span>
                  <span><button class="btn btn-primary"
                                v-on:click="toPage( temp_page )">GO</button></span>
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
            where: [],
            goodsAttributeList: [],
            goodsTypeList: [],
            attr_input_type: [],
            page: 1,
            page_count: 1,
            temp_page: 1
        },
        methods: {
            getList: function(){
                var that = this;
                $.ajax({
                    url: "",
                    type: 'post',
                    data: that.where,
                    dataType: 'json',
                    success: function(res){
                        console.log(res);
                        that.goodsAttributeList = res.goodsAttributeList;
                        that.goodsTypeList = res.goodsTypeList;
                        that.attr_input_type = res.attr_input_type;
                        that.page = res.page['page'];
                        that.temp_page = res.page['page'];
                        that.page_count = res.page['page_count'];
                    }
                });
            },
            filter: function () {
                var type_id = $('#type_id').val();
                this.where = {'type_id': type_id};
                this.getList();
            },
            changeAttrIndex: function(obj){
                if (obj.attr_index == 1){
                    obj.attr_index = 0;
                } else {
                    obj.attr_index = 1;
                }

                $.ajax({
                    url: "{:U('Shop/AdminApi/changeTableVal')}",
                    data: {
                        'table': 'goodsAttribute',
                        'id_name': 'attr_id',
                        'id_value': obj.attr_id,
                        'field': 'attr_index',
                        'value': obj.attr_index
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
                        'table': 'goodsAttribute',
                        'id_name': 'attr_id',
                        'id_value': obj.attr_id,
                        'field': 'order',
                        'value': obj.order
                    },
                    success: function(res){
                        layer.msg('操作成功');
                    }
                });
            },
            delGoodsAttr: function (id) {
                layer.confirm('确定要删除该属性吗？',{
                    btn:['确定', '取消']
                },function () {
                    $.ajax({url: "{:U('Attribute/delGoodsAttribute')}", type: 'get', data: {'id': id}, dataType: 'json',
                        success:function (res){
                            layer.alert(res.info,{
                                icon: res.status
                            },function(){
                                window.location.reload();
                            });
                        }
                    });
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
                var tempWhere = this.where;
                var temp_page = {'page': page};
                this.where = $.extend(tempWhere, temp_page);

                this.getList();
            }
        },
        mounted: function(){
            this.getList();
        }
    });

</script>
</body>
</html>