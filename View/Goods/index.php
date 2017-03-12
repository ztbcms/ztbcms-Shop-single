<include file="Public/min-header"/>
<div class="wrapper">
    <include file="Public/breadcrumb"/>
    <style>
        #search-form > .form-group {
            margin-left: 10px;
        }
    </style>
    <!-- Main content -->
    <section class="content" id="app">
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 商品列表</h3>
                </div>
                <div class="panel-body">
                    <div class="navbar navbar-default">
                        <form action="" id="search-form2" class="navbar-form form-inline" method="post"
                              onsubmit="return false">
                            <input type="hidden" name="page" v-model="page">
                            <div class="form-group">
                                <select name="cat_id" id="cat_id" class="form-control">
                                    <option value="">所有分类</option>
                                    <foreach name="categoryList" item="v" key="k">
                                        <option value="{$v['id']}"> {$v['name']}</option>
                                    </foreach>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="brand_id" id="brand_id" class="form-control">
                                    <option value="">所有品牌</option>
                                    <foreach name="brandList" item="v" key="k">
                                        <option value="{$v['id']}">{$v['name']}</option>
                                    </foreach>
                                </select>
                            </div>

                            <div class="form-group">
                                <select name="is_on_sale" id="is_on_sale" class="form-control">
                                    <option value="">全部</option>
                                    <option value="1">上架</option>
                                    <option value="0">下架</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="intro" class="form-control">
                                    <option value="0">全部</option>
                                    <option value="is_new">新品</option>
                                    <option value="is_recommend">推荐</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="input-order-id">关键词</label>
                                <div class="input-group">
                                    <input type="text" name="key_word" value="" placeholder="搜索词" id="input-order-id"
                                           class="form-control">
                                </div>
                            </div>
                            <!--排序规则-->
                            <input type="hidden" name="orderby" value="goods_id desc"/>
                            <button type="submit" v-on:click="getList"
                                    id="button-filter search-order" class="btn btn-primary"><i class="fa fa-search"></i>
                                筛选
                            </button>
                            <button type="button" onclick="location.href='{:U('Goods/addEditGoods')}'"
                                    class="btn btn-primary pull-right"><i class="fa fa-plus"></i>添加新商品
                            </button>
                        </form>
                    </div>
                    <div id="ajax_return">
                        <form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <td class="text-right">
                                            <a href="javascript:;" v-on:click="sort('goods_id')">ID</a>
                                        </td>
                                        <td class="text-left">
                                            <a href="javascript:;" v-on:click="sort('goods_name')">商品名称</a>
                                        </td>
                                        <td class="text-left">
                                            <a href="javascript:;" v-on:click="sort('goods_sn')">货号</a>
                                        </td>
                                        <td class="text-left">
                                            <a href="javascript:;" v-on:click="sort('cat_id')">分类</a>
                                        </td>
                                        <td class="text-left">
                                            <a href="javascript:;" v-on:click="sort('shop_price')">价格</a>
                                        </td>
                                        <td class="text-left">
                                            <a href="javascript:void(0);">库存</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:;" v-on:click="sort('is_on_sale')">上架</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:;" v-on:click="sort('is_recommend')">推荐</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:;" v-on:click="sort('is_new')">新品</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:;" v-on:click="sort('is_hot')">热卖</a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:;" v-on:click="sort('sort')">排序</a>
                                        </td>
                                        <td class="text-right">操作</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="item in lists">
                                        <td class="text-right">{{ item.goods_id }}</td>
                                        <td class="text-left">{{ item.goods_name }}</td>
                                        <td class="text-left">{{ item.goods_sn }}</td>
                                        <td class="text-left">{{ item.cat_name}}</td>
                                        <td class="text-left">{{ item.shop_price }}</td>
                                        <td class="text-left">
                                            <input type="text" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')"
                                                   onpaste="this.value=this.value.replace(/[^\d.]/g,'')"
                                                   v-on:change="ajaxUpdateField('goods',item.goods_id,'store_count',item.store_count)"
                                                   size="4" v-model="item.store_count"
                                            />
                                        </td>
                                        <td class="text-center">
                                            <img v-if="item.is_on_sale == 1" width="20" height="20"
                                                 src="{$config_siteurl}statics/extres/shop/images/yes.png "
                                                 v-on:click="changeTableVal('goods','goods_id',item.goods_id,'is_on_sale',0,item)"
                                                 id="img_is_on_sale"/>
                                            <img v-else width="20" height="20"
                                                 src="{$config_siteurl}statics/extres/shop/images/cancel.png "
                                                 v-on:click="changeTableVal('goods','goods_id',item.goods_id,'is_on_sale',1,item)"
                                                 id="img_is_on_sale"/>
                                        </td>
                                        <td class="text-center">
                                            <img v-if="item.is_recommend == 1" width="20" height="20"
                                                 src="{$config_siteurl}statics/extres/shop/images/yes.png "
                                                 v-on:click="changeTableVal('goods','goods_id',item.goods_id,'is_recommend',0,item)"
                                                 id="img_is_recommend"/>
                                            <img v-else width="20" height="20"
                                                 src="{$config_siteurl}statics/extres/shop/images/cancel.png "
                                                 v-on:click="changeTableVal('goods','goods_id',item.goods_id,'is_recommend',1,item)"
                                                 id="img_is_recommend"/>
                                        </td>
                                        <td class="text-center">
                                            <img v-if="item.is_new == 1" width="20" height="20"
                                                 src="{$config_siteurl}statics/extres/shop/images/yes.png "
                                                 v-on:click="changeTableVal('goods','goods_id',item.goods_id,'is_new',0,item)"
                                                 id="img_is_new"/>
                                            <img v-else width="20" height="20"
                                                 src="{$config_siteurl}statics/extres/shop/images/cancel.png "
                                                 v-on:click="changeTableVal('goods','goods_id',item.goods_id,'is_new',1,item)"
                                                 id="img_is_new"/>
                                        </td>
                                        <td class="text-center">
                                            <img v-if="item.is_hot == 1" width="20" height="20"
                                                 src="{$config_siteurl}statics/extres/shop/images/yes.png "
                                                 v-on:click="changeTableVal('goods','goods_id',item.goods_id,'is_hot',0,item)"
                                                 id="img_is_hot"/>
                                            <img v-else width="20" height="20"
                                                 src="{$config_siteurl}statics/extres/shop/images/cancel.png "
                                                 v-on:click="changeTableVal('goods','goods_id',item.goods_id,'is_hot',1,item)"
                                                 id="img_is_hot"/>
                                        </td>
                                        <td class="text-center">
                                            <input type="text" onkeyup="this.value=this.value.replace(/[^\d]/g,'')"
                                                   onpaste="this.value=this.value.replace(/[^\d]/g,'')"
                                                   v-on:change="changeTableVal('goods','goods_id',item.goods_id,'sort',item.sort,item)"
                                                   size="4" v-model=" item.sort "/>
                                        </td>
                                        <td class="text-right">
                                            <!--<a target="_blank" href="{:U('Home/Goods/goodsInfo',array('id'=>$list['goods_id']))}" class="btn btn-info" title="查看详情"><i class="fa fa-eye"></i></a>-->
                                            <a :href="'{:U('Goods/addEditGoods')}&id='+item.goods_id"
                                               class="btn btn-primary" title="编辑"><i class="fa fa-pencil"></i></a>
                                            <a href="javascript:void(0);" v-on:click="delGoods(item.goods_id)"
                                               class="btn btn-danger" title="删除"><i class="fa fa-trash-o"></i></a>
                                            <!--<a href="javascript:void(0);" onclick="ClearGoodsHtml('{$list[goods_id]}')" class="btn btn-default" title="清除静态缓存页面"><i class="fa fa-fw fa-refresh"></i></a>-->
                                            <!--<a href="javascript:void(0);" onclick="ClearGoodsThumb('{$list[goods_id]}')" class="btn btn-default" title="清除缩略图缓存"><i class="glyphicon glyphicon-picture"></i></a>-->
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>

                        <!--     分页-->
                        <v-page :page="page" v-on:update="getList" :page_count="page_count"></v-page>
                        <!--   /分页-->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<include file="Public/vue"/>
<script>
    $(document).ready(function () {
        new Vue({
            el: '#app',
            data: {
                page: 1,
                total: 0,
                page_count: 0,
                order: 'desc',
                lists: [],
            },
            mixins: [window.__baseMethods],
            methods: {
                sort: function (field) {
                    if (this.order == 'desc') {
                        this.order = 'asc'
                        var order = field + ' asc'
                    } else {
                        var order = field + ' desc'
                        this.order = 'desc'
                    }
                    $("input[name='orderby']").val(order)
                    this.getList()
                },
                delGoods: function (id) {
                    var that = this
                    if (!confirm('确定要删除吗?'))
                        return false;
                    $.ajax({
                        url: window.config.url.goods_del,
                        data: {id: id},
                        dataType: 'json',
                        success: function (v) {
                            if (v.hasOwnProperty('status') && (v.status == 1)) {
                                that.getList()
                            } else {
                                layer.msg(v.msg, {
                                    icon: 2,
                                    time: 1000
                                });
                            }
                        }
                    });
                },
                getList: function () {
                    var that = this
                    $.ajax({
                        type: "POST",
                        url: window.config.url.goods_list,
                        data: $('#search-form2').serialize(), // 你的formid
                        dataType: 'json',
                        success: function (res) {
                            console.log(res)
                            if (res.status) {
                                var data = res.info
                                that.lists = data.lists
                                that.page = data.page
                                that.total = data.total
                                that.page_count = data.page_count
                            }
                        }
                    });
                }
            },
            mounted: function () {
                this.getList()
            },
            components: {
                'v-page': pageComponent
            }
        })

    });
</script>
</body>

</html>