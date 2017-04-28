<include file="Public/min-header"/>
<div class="wrapper">
    <include file="Public/breadcrumb"/>
    <section class="content">
        <!-- Main content -->
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 商品规格</h3>
                </div>
                <div class="panel-body" id="app">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_tongyong" data-toggle="tab">商品规格</a></li>
                    </ul>
                    <!--表单数据-->
                    <form method="post" id="addEditSpecForm">
                        <!--通用信息-->
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_tongyong">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td>规格名称：</td>
                                        <td>
                                            <input type="text" v-model="detail.name" name="name" class="form-control"/>
                                            <span id="err_name" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>所属商品类型：</td>
                                        <td>
                                            <select name="type_id" id="type_id" v-model="detail.type_id"
                                                    class="form-control">
                                                <option value="">请选择</option>
                                                <option v-for="item in goodsType" :value="item.id"
                                                        v-bind:selected="item.id == detail.type_id ? 'selected' : ''">{{
                                                    item.name }}
                                                </option>
                                            </select>
                                            <span id="err_type_id" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>规格项：</td>
                                        <td>
                                            <textarea rows="5" cols="30" name="items" id="items" class="form-control">{{ specItemStr }}</textarea>
                                            每个规格项请以中文逗号，隔开
                                            <span id="err_items" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>排序：</td>
                                        <td>
                                            <input type="text" v-model="detail.order" name="order"
                                                   class="form-control"/>
                                            <span id="err_order" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="pull-right">
                            <button class="btn btn-primary" title="" data-toggle="tooltip" type="button"
                                    v-on:click="addEditGoodsType()" data-original-title="保存"><i class="fa fa-save"></i>
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
            goodsType: [],
            detail: [],
            specItemStr: []
        },
        mixins: [window.__baseMethods],
        methods: {
            getDetail: function () {
                var that = this;
                var id = that.getQueryString('id')
                that.httpGet("{:U('Spec/getSpecDetail')}", {id: id}, function (res) {
                    that.goodsType = res.goodsType;
                    that.detail = res.data;
                    that.specItemStr = res.specItemStr;
                })
            },
            addEditGoodsType: function () {
                var that = this;
                that.detail['search_index'] = 0;
                var items = $('#items').val();
                var id = that.getQueryString('id')
                var data = {
                    'detail': that.detail,
                    'items': items,
                    'id': id
                };
                that.httpPost("{:U('Spec/addEditSpec')}", data, function (res) {
                    if (res.status) {
                        layer.alert('操作成功', function () {
                            parent.layer.closeAll()
                        });
                    } else {
                        layer.alert(res.msg);
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