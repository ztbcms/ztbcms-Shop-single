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
                    <h3 class="panel-title"><i class="fa fa-list"></i> 商品属性</h3>
                </div>
                <div class="panel-body" id="app">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_tongyong" data-toggle="tab">商品属性</a></li>
                    </ul>
                    <!--表单数据-->
                    <form method="post" id="addEditGoodsAttributeForm">                    
                        <!--通用信息-->
                    <div class="tab-content">                 	  
                        <div class="tab-pane active" id="tab_tongyong">
                           
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td>属性名称：</td>
                                    <td>
                                        <input type="text" v-model="detail.attr_name" name="attr_name"/>
                                        <span id="err_attr_name" style="color:#F00; display:none;"></span>                                        
                                    </td>
                                </tr>  
                                <tr>
                                    <td>所属商品类型：</td>
                                    <td>
                                        <select name="type_id" id="type_id" v-model="detail.type_id">
                                            <option value="">请选择</option>
                                            <option v-for="item in goodsType" :value="item.id" v-bind:selected="item.id == detail.type_id ? 'selected' : ''">{{ item.name }}</option>
                                        </select>
                                        <span id="err_type_id" style="color:#F00; display:none;"></span>                                        
                                    </td>
                                </tr>  
                                <tr>
                                    <td>能否进行检索：</td>
                                    <td>
                                        <input type="radio" value="0" name="attr_index" v-bind:checked="detail.attr_index == 0 ? 'checked' : ''" >不需要检索
                                        <input type="radio" value="1" name="attr_index" v-bind:checked="detail.attr_index == 1 || detail.attr_index == '' ? 'checked' : ''" />关键字检索
                                        <!--<input type="radio" value="2" name="attr_index" <if condition="$goodsAttribute[attr_index] eq 2">checked="checked"</if>  />范围检索-->
                                    </td>
                                </tr>
                                <tr>
                                    <td>该属性值的录入方式：</td>
                                    <td>
                                        <input type="radio" value="0" name="attr_input_type" v-bind:checked="detail.attr_input_type == 0 || detail.attr_input_type == '' ? 'checked' : ''"/>手工录入
                                        <input type="radio" value="1" name="attr_input_type" v-bind:checked="detail.attr_input_type == 1 ? 'checked' : ''"/>从下面的列表中选择（一行代表一个可选值）
                                        <input type="radio" value="2" name="attr_input_type" v-bind:checked="detail.attr_input_type == 2 ? 'checked' : ''"/>多行文本框
                                    </td>
                                </tr>  
                                <tr>
                                    <td>可选值列表：</td> 
                                    <td>
                                    <textarea rows="5" cols="30" name="attr_values" id="attr_values">{{detail.attr_values}}</textarea>
                                    录入方式为手工或者多行文本时，此输入框不需填写。
                                    <span id="err_attr_values" style="color:#F00; display:none;"></span>
                                    </td>
                                </tr>                                
                                </tbody>                                
                                </table>
                        </div>                           
                    </div>              
                    <div class="pull-right">
                        <button class="btn btn-primary" title="" data-toggle="tooltip" type="button" v-on:click="addEditGoodsAttribute()" data-original-title="保存"><i class="fa fa-save"></i></button>
                    </div>
			    </form><!--表单数据-->
                </div>
            </div>
        </div>    <!-- /.content -->
    </section>
</div>
<include file="Public/vue"/>
<script>
    new Vue({
        el: '#app',
        data: {
            goodsType: [],
            detail: [],
            specItemStr: []
        },
        methods: {
            getDetail: function(){
                var that = this;
                $.ajax({url: "{:U('Attribute/getAttrDetail')}", type: 'post', data: {'id': '<?php echo $id;?>'}, dataType: 'json',
                    success:function (res){
                        console.log(res);
                        that.goodsType = res.goodsType;
                        that.detail = res.data;
                        that.specItemStr = res.specItemStr;
                    }
                });
            },
            addEditGoodsAttribute: function(){
                var that = this;
                that.detail['attr_index'] = 0;
                that.detail['attr_type'] = 0;
                that.detail['order'] = 50;
                that.detail['attr_values'] = $('#attr_values').val();
                that.detail['attr_index'] = $('input[name="attr_index"]:checked').val();
                that.detail['attr_input_type'] = $('input[name="attr_input_type"]:checked').val();

                var data = {
                    'detail': that.detail,
                    'id': '<?php echo $id;?>'
                };

                $.ajax({url: "{:U('Attribute/addEditGoodsAttribute')}", type: 'post', data: data, dataType: 'json',
                    success:function (res){
                        if(res.status){
                            layer.alert('操作成功',function(){
                                window.location.href = "{:U('Attribute/index')}";
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
</script>
</body>
</html>