<include file="Public/min-header"/>
<div class="wrapper">
    <include file="Public/breadcrumb"/>
    <section class="content">
        <!-- Main content -->
        <div class="container-fluid">
            <div class="pull-right">
                <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 品牌详情</h3>
                </div>
                <div class="panel-body" id="app">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_tongyong" data-toggle="tab">商品类型</a></li>
                    </ul>
                    <!--表单数据-->
                    <form method="post" id="addEditBrandForm" onsubmit="return false;">
                        <!--通用信息-->
                    <div class="tab-content">                 	  
                        <div class="tab-pane active" id="tab_tongyong">
                           
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td>品牌名称:</td>
                                    <td>
                                        <input type="text" v-model="brand.name" name="name" class="form-control" style="width:200px;"/>
                                        <span id="err_name" style="color:#F00; display:none;">品牌名称不能为空</span>                                        
                                    </td>
                                </tr>                                
                                <tr>
                                    <td>品牌网址:</td>
                                    <td>
                                        <input type="text" v-model="brand.url" name="url" class="form-control" style="width:250px;"/>
                                        <span id="err_url" style="color:#F00; display:none;"></span>                                        
                                    </td>
                                </tr>                                                                
                                <tr>
                                    <td>所属分类:</td>
                                    <td>
                                        <div class="col-sm-3">
	                                        <select name="parent_cat_id" id="parent_id_1" v-model="brand.parent_cat_id" class="form-control" style="width:250px;margin-left:-15px;">
                                                <option value="0">顶级分类</option>
                                                <option v-for="item in catList" :value="item.id" v-bind:selected="item.id == brand['parent_cat_id'] ? 'selected' : ''">{{ item.name }}</option>
						                    </select>
	                                    </div>                                    
	                                    <div class="col-sm-3">
	                                      <select name="cat_id" id="parent_id_2" v-model="brand.cat_id" class="form-control" style="width:250px;">
                                              <option value="0">请选择分类</option>
                                              <option v-for="item in catList" v-if="item.parent_id == brand.parent_cat_id" :value="item.id" v-bind:selected="item.id == brand['cat_id'] ? 'selected' : ''">{{ item.name }}</option>
	                                      </select>  
	                                    </div>     
                                    </td>
                                </tr>                                
                                <tr>
                                    <td>品牌logo:</td>
                                    <td>  
                                    	<div class="col-sm-3">                                                                              
                                        	<input type="text" v-model="brand.logo" name="logo" id="logo" class="form-control" style="width:350px;margin-left:-15px;"/>
                                        </div>
                                        <div class="col-sm-3">
                                        	<input onclick="GetUploadify(1,'logo','brand');" type="button" class="btn btn-default" value="上传logo"/>
                                        </div>
                                    </td>
                                </tr> 
                                <tr>
                                    <td>品牌排序:</td>
                                    <td>
                                        <input type="text" v-model="brand.sort" name="sort" class="form-control" style="width:200px;" placeholder="50"/>
                                    </td>
                                </tr>                                                                 
                                <tr>
                                    <td>品牌描述:</td>
                                    <td>
										<textarea rows="4" cols="60" name="desc" v-model="brand.desc" >{{ brand.desc }}</textarea>
                                        <span id="err_desc" style="color:#F00; display:none;"></span>                                        
                                    </td>
                                </tr>                                  
                                </tbody>                                
                                </table>
                        </div>                           
                    </div>              
                    <div class="pull-right">
                        <button v-on:click="addEditBrand()" class="btn btn-primary" data-toggle="tooltip" type="submit" data-original-title="保存">保存</button>
                    </div>
			    </form><!--表单数据-->
                </div>
            </div>
        </div>    <!-- /.content -->
    </section>
</div>
<include file="Public/vue"/>
<script>
    var obj = new Vue({
        el: '#app',
        data: {
            brand: [],
            catList: []
        },
        methods: {
            getDetail: function(){
                var that = this;
                $.ajax({url: "{:U('Brand/getBrandDetail')}", type: 'post', data: {'id': '<?php echo $id;?>'}, dataType: 'json',
                    success:function (res){
                        console.log(res);
                        that.brand = res.brand;
                        that.catList = res.cat_list;
                    }
                });
            },
            addEditBrand: function(){
                var that = this;
                var data = {
                    'detail': that.brand,
                    'id': '<?php echo $id;?>'
                };

                $.ajax({url: "{:U('Brand/addEditBrand')}", type: 'post', data: data, dataType: 'json',
                    success:function (res){
                        if(res.status){
                            layer.alert('操作成功',function(){
                                window.location.href = "{:U('Brand/index')}";
                            });
                        }else{
                            layer.alert('操作失败');
                        }
                    }
                });
            }
        },
        mounted: function(){
            this.getDetail();
        }
    });

    $('#parent_id_1').change(function(){
        obj.brand['cat_id'] = 0;
    });
</script>
</body>
</html>