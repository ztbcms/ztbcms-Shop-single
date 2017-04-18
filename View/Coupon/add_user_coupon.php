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
                    <h3 class="panel-title"><i class="fa fa-list"></i> 赠送优惠券</h3>
                </div>
                <div class="panel-body" id="app">
                    <!--表单数据-->
                    <form id="coupon-add">
                        <div class="tab-pane">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td>选择优惠券</td>
                                    <td>
                                        <div class="form-group ">
                                            <div class="col-xs-8">
                                                <select id="coupon_id" name="coupon_id" class="form-control"
                                                        v-model="selectCouponId">
                                                    <template v-for="item in couponLists">
                                                        <option :value="item.id">{{ item.description }}</option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>赠送对象</td>
                                    <td>
                                        <div class="form-group ">
                                            <div class="col-xs-5">
                                                <select id="userid" name="userid" class="form-control"
                                                        v-model="selectUserId">
                                                    <template v-for="item in userLists">
                                                        <option :value="item.userid">{{ item.nickname }}</option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <a class="btn btn-info" @click="checkSubmit()">
                            <i class="ace-icon fa fa-check bigger-110"></i>
                            保存
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<include file="Public/vue"/>
<script>
    new Vue({
        el:'#app',
        data:{
            couponLists:[],
            userLists:[],
            selectCouponId:'',
            selectUserId:''
        },
        mixins:[window.__baseMethods,window.__baseFilters],
        mounted:function () {
            this.getCouponLists()
            this.getUserLists()
        },
        methods:{
            getCouponLists:function () {
                var that = this
                that.httpPost('index.php?g=Shop&m=Coupon&a=getCouponLists',{},function (res) {
                    if(res.status == 1){
                        that.couponLists = res.info
                    }
                })
            },
            getUserLists:function () {
                var that = this
                that.httpPost('index.php?g=Shop&m=Coupon&a=getUserLists',{},function (res) {
                    if(res.status == 1){
                        that.userLists = res.info
                    }
                })
            },
            checkSubmit:function () {
                if(this.selectCouponId == ''){
                    layer.msg('请选择优惠券');
                }else if(this.selectUserId == ''){
                    layer.msg('请选择会员');
                }else{
                    var that = this
                    var data = {
                        coupon_id:that.selectCouponId,
                        userid:that.selectUserId
                    }
                    that.httpPost('index.php?g=Shop&m=Coupon&a=add_user_coupon',data,function (res) {
                        
                    })
                }
            }
        }
    })
//    function checkSubmit() {
//        $("span[id^='err_']").each(function() {
//            $(this).hide()
//        });
//        if($('#discount_price').val() == '') {
//            $('#err_discount_price').show()
//        }else if($('#description').val() == ''){
//            $('#err_description').show()
//        }else{
//            var data = $("#coupon-add").serialize()
//            $.post("{:U('Coupon/add_coupon')}",data,function (res) {
//                if(res.status){
//                    layer.alert(res.msg,function(){
//                        window.location.href = "{:U('Coupon/index')}";
//                    });
//                }else{
//                    layer.alert(res.msg);
//                }
//            },'json')
//        }
//    }
</script>
</body>

</html>