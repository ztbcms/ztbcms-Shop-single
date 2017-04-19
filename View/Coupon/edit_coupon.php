<include file="Public/min-header" />
<div class="wrapper">
    <include file="Public/breadcrumb" />
    <section class="content">
        <!-- Main content -->
        <div class="container-fluid">
            <div class="pull-right">
                <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 修改商城优惠券信息</h3>
                </div>
                <div class="panel-body">
                    <!--表单数据-->
                    <form id="coupon-edit">
                        <div class="tab-pane">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td>优惠价格:</td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-xs-2">
                                                <input name="discount_price" id="discount_price" value="{$coupon.discount_price}" class="form-control" placeholder="优惠价格" />
                                            </div>
                                            <div class="col-xs-4">
                                                <span id="err_discount_price" style="color:#F00; display:none;">优惠价格不能为空</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>满减价格:</td>
                                    <td>
                                        <div class="form-group ">
                                            <div class="col-xs-2">
                                                <input name="full_price" id="full_price" value="{$coupon.full_price}" class="form-control" placeholder="满减价格" />
                                            </div>
                                            <span>例如100，满100才能减，无条件默认是0</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>优惠类型</td>
                                    <td>
                                        <div class="form-group ">
                                            <div class="col-xs-2">
                                                <select id="type" name="type" class="form-control">
                                                        <option <if condition="$coupon[type] eq 0">selected</if> value="0" >不可叠加</option>
                                                        <option <if condition="$coupon[type] eq 1">selected</if> value="1" >可叠加</option>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>优惠券状态</td>
                                    <td>
                                        <div class="form-group ">
                                            <div class="col-xs-2">
                                                <select id="status" name="status" class="form-control">
                                                    <option <if condition="$coupon[status] eq 0">selected</if> value="0" >无效</option>
                                                    <option <if condition="$coupon[status] eq 1">selected</if> value="1" >正常</option>
                                                    <option <if condition="$coupon[status] eq 2">selected</if> value="2" >过期</option>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>使用起始时间:</td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-xs-4">
                                                <input name="start_time" type="date" value="{$coupon['start_time']}" class="form-control" placeholder="使用起始时间" />
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>过期时间:</td>
                                    <td>
                                        <div class="form-group">
                                            <div class="col-xs-4">
                                                <input name="end_time" type="date" value="{$coupon['end_time']}" class="form-control" placeholder="过期时间" />
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>优惠券说明:</td>
                                    <td>
                                        <div class="form-group ">
                                            <div class="col-xs-5">
                                                <textarea style="width:300px; height:150px;" name="description" id="description">{$coupon.description}</textarea>
                                            </div>
                                            <div class="col-xs-4">
                                                <span id="err_description" style="color:#F00; display:none;">优惠券说明不能为空</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="id" value="{$coupon.id}">
                        <a class="btn btn-info" onclick="checkSubmit()">
                            <i class="ace-icon fa fa-check bigger-110"></i>
                            保存
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<script>

    function checkSubmit() {
        $("span[id^='err_']").each(function() {
            $(this).hide()
        });
        if($('#discount_price').val() == '') {
            $('#err_discount_price').show()
        }else if($('#description').val() == ''){
            $('#err_description').show()
        }else{
            var data = $("#coupon-edit").serialize()
            $.post("{:U('Coupon/edit_coupon')}",data,function (res) {
                if(res.status){
                    layer.alert(res.msg)
                    setTimeout(function () {
                        window.location.href = "{:U('Coupon/index')}";
                    }, 1500)
                }else{
                    layer.alert(res.msg);
                }
            },'json')
        }
    }
</script>
</body>

</html>