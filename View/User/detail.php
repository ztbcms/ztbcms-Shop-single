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
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> 用户信息</h3>
            </div>
            <div class="panel-body">
                <form action="" method="post" onsubmit="return checkUserUpdate(this);">
                    <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <td class="col-sm-2">会员昵称:</td>
                        <td ><input type="text" class="form-control" name="nickname" value="{$user.nickname}"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>邮件地址:</td>
                        <td><input type="text" class="form-control" name="email" value="{$user.email}"></td>
                        <td>电子邮箱</td>
                    </tr>
                    <tr>
                        <td>新密码:</td>
                        <td><input type="password" class="form-control" name="password"></td>
                        <td>留空表示不修改密码</td>
                    </tr>
                    <tr>
                        <td>确认密码:</td>
                        <td><input type="password" class="form-control" name="password2" ></td>
                        <td>留空表示不修改密码</td>
                    </tr>
                    <!--<tr>-->
                        <!--<td>会员等级:</td>-->
                        <!--<td>{$user.user_rank}</td>-->
                    <!--</tr>-->
                    <tr>
                        <td>性别:</td>
                        <td id="order-status">
                            <input name="sex" type="radio" value="0" <if condition="$user['sex'] eq 0">checked</if> >保密
                            <input name="sex" type="radio" value="1" <if condition="$user['sex'] eq 1">checked</if> >男
                            <input name="sex" type="radio" value="2" <if condition="$user['sex'] eq 2">checked</if> >女
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>手机:</td>
                        <td>
                            <input type="text" class="form-control" name="mobile" value="{$user.mobile}">
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>冻结用户:</td>
                        <td>
                            <input name="islock" type="radio" value="1" <if condition="$user['islock'] eq 1">checked</if> >是
                            <input name="islock" type="radio" value="0" <if condition="$user['islock'] eq 0">checked</if> >否
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>上次登录时间:</td>
                        <td>
                            {$user.lastdate|date='Y-m-d H:i',###}
                        </td>
                        <td></td>
                    </tr>
                        
                    <tr>
                        <td>是否分销:</td>
                        <td id="order-status">                            
                            <input name="is_distribut" type="radio" value="1" <if condition="$user['is_distribut'] eq 1">checked</if> >是
                            <input name="is_distribut" type="radio" value="0" <if condition="$user['is_distribut'] eq 0">checked</if> >否
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="300">上一级编号</td>
                        <td>
                            <input type="text" class="form-control" name="first_leader" value="{$user.first_leader}">
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>上二级编号</td>
                        <td>
                            <input type="text" class="form-control" name="second_leader" value="{$user.second_leader}">
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>上三级编号</td>
                        <td>
                            <input type="text" class="form-control" name="third_leader" value="{$user.third_leader}">
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <button type="submit" class="btn btn-info">
                                <i class="ace-icon fa fa-check bigger-110"></i> 保存
                            </button>
                            <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default pull-right" data-original-title="返回"><i class="fa fa-reply"></i></a>
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
    function checkUserUpdate(){
        var email = $('input[name="email"]').val();
        var mobile = $('input[name="mobile"]').val();
        var password = $('input[name="password"]').val();
        var password2 = $('input[name="password2"]').val();

        var error ='';
        if(password != password2){
            error += "两次密码不一样\n";
        }

        if(email && !checkEmail(email)){
            error += "邮箱地址有误\n";
        }
        if(mobile && !checkMobile(mobile)){
            error += "手机号码填写有误\n";
        }
        if(!email && !mobile){
            error += "手机邮箱至少填一个\n";
        }
        if(error){
            layer.alert(error, {icon: 2});  //alert(error);
            return false;
        }
        return true;

    }
</script>

</body>
</html>