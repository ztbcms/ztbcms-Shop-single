<include file="Public/min-header"/>
<div class="wrapper">
    <include file="Public/breadcrumb"/>
    <section class="content">
        <!-- Main content -->
        <div class="container-fluid">
            <div class="pull-right">
                <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
            </div>
            <div class="panel panel-default" id="app">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 商品类型</h3>
                </div>
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_tongyong" data-toggle="tab">商品类型</a></li>
                    </ul>
                    <!--表单数据-->
                    <form method="post" id="addEditGoodsTypeForm" onsubmit="return false;">
                        <!--通用信息-->
                    <div class="tab-content">                 	  
                        <div class="tab-pane active" id="tab_tongyong">
                           
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td>类型名称:</td>
                                    <td>
                                        <input type="text" v-model="detail.name" name="name"/>
                                        <span id="err_name" style="color:#F00; display:none;">商品类型名称不能为空!!</span>
                                    </td>
                                </tr>                                
                                </tbody>                                
                                </table>
                        </div>                           
                    </div>              
                    <div class="pull-right">
                        <input type="hidden" name="id" :value="detail.id">
                        <button v-on:click="addEditGoodsType()" class="btn btn-primary" title="" data-toggle="tooltip" data-original-title="保存"><i class="fa fa-save"></i> 确认</button>
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
            detail:{}
        },
        methods: {
            getDetail: function(){
                var that = this;
                $.ajax({url: "{:U('Type/getTypeDetail')}", type: 'post', data: {'id': '<?php echo $id;?>'}, dataType: 'json',
                    success:function (res){
                        if(res.status){
                            that.detail = res.data;
                        }
                    }
                });
            },
            addEditGoodsType: function(){
                var that = this;
                $.ajax({url: "{:U('Type/addEditGoodsType')}", type: 'post', data: that.detail, dataType: 'json',
                    success:function (res){
                        if(res.status){
                            layer.alert('操作成功',function(){
                                window.location.href = "{:U('Type/index')}";
                            });
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