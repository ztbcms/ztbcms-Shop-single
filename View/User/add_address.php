<include file="Public/min-header"/>
<div class="wrapper">
    <!-- Content Header (Page header) -->
   <include file="Public/breadcrumb"/>
    <section class="content">
    <!-- Main content -->
    <!--<div class="container-fluid">-->
    <div class="row">
      <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="pull-right">
                <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
            </div>
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> 添加地址</h3>
            </div>
            <div class="panel-body">
                <form action="" method="post">
                    <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <td class="col-sm-2">收货人:</td>
                        <td ><input type="text" class="form-control" name="consignee" value=""></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>联系方式:</td>
                        <td><input type="text" class="form-control" name="mobile"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>邮政编码:</td>
                        <td><input type="text" class="form-control" name="zipcode" value=""></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>省/市/区:</td>
                        <td id="order-status">
                            <select class="form-control" style="width: auto;" name="province" id="">
                                <get sql="SELECT * FROM cms_area_province " page="$page" num="100">
                                    <volist name="data" id="vo">
                                        <option value="{$vo.id}">{$vo.areaname}</option>
                                    </volist>
                                </get>
                            </select>

                            <select class="form-control" style="width: auto;" name="city" id=""></select>

                            <select class="form-control" style="width: auto;" name="district" id=""></select>

                            <template id="tpl_option">
                                <option value="{id}">{name}</option>
                            </template>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>详细地址:</td>
                        <td><input type="text" class="form-control" name="address" value=""></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <button type="submit" class="btn btn-info">
                                <i class="ace-icon fa fa-check bigger-110"></i> 添加
                            </button>
                            <input type="hidden" name="userid" value="{:I('id')}">
                            <input type="reset" class="btn btn-default pull-right" value="重置">
                        </td>
                    </tr>
                    </tbody>
                </table>
                </form>

            </div>
        </div>
 	  </div> 
    </div>    <!-- /.content -->
   </section>
</div>
<script>
    (function($){
        var $province = $('select[name=province]');
        var $city = $('select[name=city]');
        var $district = $('select[name=district]');
        var tpl_option = $('#tpl_option').html();

        //省份切换
        $province.on('change', function(){
            $.ajax({
                'url': "{:U('Area/Api/getCitiesByProvinceId')}" + '&id=' + $province.val(),
                'type': 'GET',
                'dataType': 'json',
                'success': function(res){
                    console.log(res.data);

                    var html = '';
                    res.data.forEach(function(item){
                        html += tpl_option.replace('{id}', item.id).replace('{name}', item.areaname);
                    });
                    $city.html(html);
                    $city.trigger('change');
                }
            });
        });

        //城市切换
        $city.on('change', function(){
            $.ajax({
                'url': "{:U('Area/Api/getDistrictsByCityId')}" + '&id=' + $city.val(),
                'type': 'GET',
                'dataType': 'json',
                'success': function(res){
                    console.log(res.data);

                    var html = '';
                    res.data.forEach(function(item){
                        html += tpl_option.replace('{id}', item.id).replace('{name}', item.areaname);
                    });
                    $district.html(html);
                }
            });
        });

        //触发初始化
        $province.trigger('change');

    })(jQuery);
</script>
</body>
</html>