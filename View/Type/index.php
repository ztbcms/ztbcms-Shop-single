<include file="Public/min-header"/>
<div class="wrapper">
  <include file="Public/breadcrumb"/>
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="fa fa-list"></i> 商品类型列表</h3>
        </div>
        <div class="panel-body">    
		<div class="navbar navbar-default">
            <div class="row navbar-form">
                <button type="submit" onclick="location.href='{:U('Type/getTypeDetail')}'"  class="btn btn-primary pull-right"><i class="fa fa-plus"></i>新增商品类型</button>
            </div>
          </div>
                        
          <div id="app">
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
                                    <a :href="'{:U('Spec/index')}&type_id='+item.id" data-toggle="tooltip" title="" class="btn btn-info" data-original-title="属性列表"><i class="fa fa-eye"></i></a>
                                    <a :href="'{:U('Type/getTypeDetail')}&id='+item.id" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="编辑"><i class="fa fa-pencil"></i></a>
                                    <a href="javascript:;" v-on:click="delGoodsType(item.id)" data-toggle="tooltip" title="" class="btn btn-danger" data-original-title="删除"><i class="fa fa-trash-o"></i></a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
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
            lists:[],
            page: 1,
            page_count: 1,
            temp_page: 1
        },
        methods: {
            getList: function(){
                var that = this;
                $.ajax({url: '', type: 'post', data: {'page': that.page}, dataType: 'json',
                    success:function (res){
                        if(res.status){
                            that.lists = res.data;
                            that.page = res.page['page'];
                            that.temp_page = res.page['page'];
                            that.page_count = res.page['page_count'];
                        }
                    }
                });
            },
            delGoodsType: function (id) {
                layer.confirm('确定要删除该分类吗？',{
                    btn:['确定', '取消']
                },function () {
                    $.ajax({url: "{:U('Type/delGoodsType')}", type: 'get', data: {'id': id}, dataType: 'json',
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
                this.page = page;
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