<include file="Public/min-header"/>
<div class="wrapper">
  <include file="Public/breadcrumb"/>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="navbar navbar-default">
                <div class="navbar-form">
                    <div class="pull-right">
                        <a data-original-title="返回" class="btn btn-default" title="" data-toggle="tooltip" href="javascript:history.go(-1)"><i class="fa fa-reply"></i></a>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 收货地址列表</h3>
                    <a href="{:U('User/add_address')}&id={:I('id')}" class="btn btn-info pull-right" style="margin-top: -25px">新增地址</a>
                </div>


                <div class="panel-body">


                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>

                                <td class="text-left">
                                    收货人
                                </td>


                                <td class="text-left">
                                    联系方式
                                </td>

                                <td class="text-left">
                                    邮政编码
                                </td>

                                <td class="text-left">
                                地址
                                 </td>

                                <td  class="text-left">操作</td>

                            </tr>
                            </thead>
                            <tbody>
                            <volist name="lists" id="list">
                                <tr>
                                    <td class="text-left">{$list.consignee}</td>

                                    <td class="text-left">{$list.mobile}</td>
                                    <td class="text-left">
                                        {$list.zipcode}
                                    </td>
                                    <td class="text-left">
                                        {$province[$list[province]]},{$city[$list[city]]},{$district[$list[district]]},{$list.address}
                                    </td>
                                    <td class="text-left">
                                        <if condition="$list[is_default] == 0">
                                            <button onclick="setDefault({$list[address_id]})" class="btn btn-danger">设为默认</button>
                                            <else/>
                                            <button id="default" value="{$list[address_id]}" class="btn btn-success disabled">默认地址</button>
                                        </if>
                                        <a href="{:U('User/update_address')}&id={$list[address_id]}" class="btn btn-info">修改地址</a>
                                        <button onclick="del({$list[address_id]})" class="btn btn-info">删除</button>
                                    </td>
                                </tr>
                            </volist>
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script>
    function setDefault(id){
        layer.confirm('确定要设置此地址为默认地址？', {
            btn: ['确定', '取消'] //按钮
        }, function () {
            var default_id = $('#default').val();
            $.ajax({
                url: '{:U("User/setDefault_address")}',
                data: {'address_id': id, 'default_id': default_id},
                type: 'post',
                dataType: 'json',
                success: function(res){
                    layer.alert(res.msg,function(){
                        window.location.reload();
                    });
                }
            });
        });
    }

    function del(id){
        layer.confirm('确定要删除？', {
            btn: ['确定', '取消'] //按钮
        },function(){
            $.ajax({
                url: '{:U("User/del_address")}',
                data: {'id': id},
                type: 'post',
                dataType: 'json',
                success: function(res){
                    layer.alert(res.msg,function(){
                        window.location.reload();
                    });
                }
            });
        });
    }
</script>
</body>
</html>