<include file="Public/min-header"/>
<link href="{$config_siteurl}statics/extres/shop/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
<script src="{$config_siteurl}statics/extres/shop/plugins/daterangepicker/moment.min.js" type="text/javascript"></script>
<script src="{$config_siteurl}statics/extres/shop/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
<div class="wrapper">
    <include file="Public/breadcrumb"/>
	<section class="content">
       <div class="row">
       		<div class="col-xs-12">
	       		<div class="box">
	           	<div class="box-header">
                    <div class="navbar navbar-default">
                            <form action="{:U('Order/order_log')}" id="search-form2" class="navbar-form form-inline" method="post">

                                <div class="form-group">
                                    <label class="control-label" for="input-order-id">订单编号</label>
                                    <div class="input-group">
                                        <input type="text" name="order_sn" placeholder="订单编号" id="input-order-id" class="input-sm">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="input-date-added">操作日期</label>
                                    <div class="input-group">
                                        <input type="text" name="timegap" value="{$timegap}" placeholder="操作时间"  id="add_time" class="input-sm">
					                 </div>
                                </div>
                                <div class="form-group">
                                    <select name="admin_id" class="input-sm">
                                  	    <option value="0">选择管理员</option>
            						    <foreach name="admin" item="vv" key="key">
                                      	<option value="{$key}">{$vv}</option>
                                        </foreach>
                                    </select>
                                </div>

                                <div class="form-group">
                                	<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> 筛选</button>
                                </div>   
                            </form>
                    </div>             
	            </div>	    
	             <!-- /.box-header -->
	             <div class="box-body">	             
	           		<div class="row">
	            	<div class="col-sm-12">
		              <table id="list-table" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
		                 <thead>
		                   <tr role="row">
			                   <th class="sorting">订单ID</th>
                               <th class="sorting">操作人</th>
                               <th class="sorting">操作</th>
			                   <th class="sorting">备注</th>
			                   <th class="sorting">操作时间</th>
			                   <th>查看</th>
		                   </tr>
		                 </thead>
						<tbody>
						  <foreach name="list" item="vo" key="k" >
						  	<tr role="row">
		                     <td>{$vo.order_id}</td>
                             <td>{$vo.action_user}</td>
		                     <td>{$vo.action_note}</td>
		                     <td>{$vo.status_desc}</td>
		                     <td>{$vo.log_time|date='Y-m-d H:i',###}</td>
		                     <td>
		                        <a href="{:U('Order/detail',array('order_id'=>$vo['order_id']))}" data-toggle="tooltip" title="" class="btn btn-info" data-original-title="查看详情"><i class="fa fa-eye"></i></a>
                             </td>
		                   </tr>
		                  </foreach>
		                   </tbody>
		                 <tfoot>
		                 
		                 </tfoot>
		               </table>
	               </div>
	          </div>
              <div class="row">
              	    <div class="col-sm-6 text-left"></div>
                    <div class="col-sm-6 text-right">{$page}</div>		
              </div>
	          </div><!-- /.box-body -->
	        </div><!-- /.box -->
       	</div>
       </div>
   </section>
</div>
<script>
$(document).ready(function(){
	$('#add_time').daterangepicker({
		format:"YYYY/MM/DD",
		singleDatePicker: false,
		showDropdowns: true,
		minDate:'2016/01/01',
		maxDate:'2030/01/01',
		startDate:'2016/01/01',
	    locale : {
            applyLabel : '确定',
            cancelLabel : '取消',
            fromLabel : '起始时间',
            toLabel : '结束时间',
            customRangeLabel : '自定义',
            daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
            monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月','七月', '八月', '九月', '十月', '十一月', '十二月' ],
            firstDay : 1
        }
	});
});
</script>  
</body>
</html>