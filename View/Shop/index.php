<include file="Public/min-header"/>
<style>
    td {
        padding: 10px 5px;
    }
</style>
<div class="wrapper">
    <include file="Public/breadcrumb"/>
    <style>
        #search-form > .form-group {
            margin-left: 10px;
        }
    </style>
    <!-- Main content -->
    <section class="content" id="app">
        <form action="{:U('index')}" method="post">
            <table style="width: 100%;">
                <tr>
                    <td colspan="2" style="background: #eee;">
                        店铺设置
                        <span style="color: red;">【根据自身的业务逻辑可以增加或删除店铺配置信息】</span>
                    </td>
                </tr>
                <tr>
                    <td width="100px"><label for="">店铺名称</label></td>
                    <td>
                        <input placeholder="请输入店铺名称" value="{$config.shop_name}" name="shop_name" style="width:300px;"
                               class="form-control"
                               type="text">
                    </td>

                </tr>
                <tr>
                    <td width="100px"><label for="">总代提成</label></td>
                    <td>
                        <input placeholder="总代提成" value="{$config.shop_first_commission}" name="shop_first_commission"
                               style="width:300px;" class="form-control"
                               type="text">
                    </td>
                </tr>
                <tr>
                    <td width="100px"><label for="">一级代理提成</label></td>
                    <td>
                        <input placeholder="一级代理提成" value="{$config.shop_second_commission}"
                               name="shop_second_commission"
                               style="width:300px;" class="form-control"
                               type="text">
                    </td>
                </tr>
                <tr>
                    <td width="100px"><label for="">经销商提成</label></td>
                    <td>
                        <input placeholder="经销商提成" value="{$config.shop_third_commission}" name="shop_third_commission"
                               style="width:300px;" class="form-control"
                               type="text">
                    </td>
                </tr>
            </table>
            <div>
                <button class="btn btn-primary" type="submit">保存</button>
            </div>
        </form>
    </section>
</div>
<include file="Public/vue"/>
<script>
    $(document).ready(function () {
    });
</script>
</body>

</html>