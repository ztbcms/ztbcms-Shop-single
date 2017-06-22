<include file="Public/min-header"/>
<div class="wrapper">
    <include file="Public/breadcrumb"/>
    <section class="content">
        <!-- Main content -->
        <div class="container-fluid">
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
                                            <input type="text" v-model="detail.name" name="name" class="form-control"/>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="pull-right">
                            <input type="hidden" name="id" :value="detail.id">
                            <button @click="addEditGoodsType()" class="btn btn-primary" title=""
                                    data-toggle="tooltip" data-original-title="保存"><i class="fa fa-save"></i> 确认
                            </button>
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
            detail: {}
        },
        mixins: [window.__baseMethods],
        methods: {
            getDetail: function () {
                var that = this;
                var id = that.getQueryString('id')
                that.httpGet("{:U('Type/getTypeDetail')}", {id: id}, function (res) {
                    if (res.status) {
                        that.detail = res.data;
                    }
                })
            },
            addEditGoodsType: function () {
                var that = this;
                if (!that.detail.name) {
                    layer.alert('请输入类型名称')
                    return;
                }
                that.httpPost("{:U('Type/addEditGoodsType')}", that.detail, function (res) {
                    if (res.status) {
                        layer.alert('操作成功', function () {
                            parent.layer.closeAll()
                        });
                    }
                })
            }
        },
        mounted: function () {
            this.getDetail();
        }
    });
</script>

</body>
</html>