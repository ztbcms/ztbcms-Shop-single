<include file="Public/min-header"/>
<!--物流配置 css -start-->
<style>
    ul.group-list {
        width: 96%;
        min-width: 1000px;
        margin: auto 5px;
        list-style: disc outside none;
    }

    ul.group-list li {
        white-space: nowrap;
        float: left;
        width: 150px;
        height: 25px;
        padding: 3px 5px;
        list-style-type: none;
        list-style-position: outside;
        border: 0px;
        margin: 0px;
    }
</style>
<!--物流配置 css -end-->

<!--以下是在线编辑器 代码 -->
<include file="ueditor"/>
<!--以上是在线编辑器 代码  end-->
<div id="app" class="wrapper">
    <include file="Public/breadcrumb"/>
    <section class="content">
        <!-- Main content -->
        <div class="container-fluid">
            <div class="pull-right">
                <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default"
                   data-original-title="返回"><i class="fa fa-reply"></i></a>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i>商品详情</h3>
                </div>
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_tongyong" data-toggle="tab">通用信息</a></li>
                        <!--                        <li><a href="#tab_goods_desc" data-toggle="tab">描述信息</a></li>-->
                        <li><a href="#tab_goods_images" data-toggle="tab">商品相册</a></li>
                        <li><a href="#tab_goods_spec" data-toggle="tab">商品规格</a></li>
                        <li><a href="#tab_goods_attr" data-toggle="tab">商品属性</a></li>
                        <!--<li><a href="#tab_goods_shipping" data-toggle="tab">商品物流</a></li>-->
                    </ul>
                    <!--表单数据-->
                    <form method="post" id="addEditGoodsForm">

                        <!--通用信息-->
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_tongyong">

                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td>商品名称:</td>
                                        <td>
                                            <input type="text" id="goods_name" value="{$goodsInfo.goods_name}"
                                                   name="goods_name"
                                                   class="form-control" style="width:550px;"/>
                                            <span id="err_goods_name" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品简介:</td>
                                        <td>
                                            <textarea rows="3" cols="80"
                                                      name="goods_remark">{$goodsInfo.goods_remark}</textarea>
                                            <span id="err_goods_remark" style="color:#F00; display:none;"></span>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品货号</td>
                                        <td>
                                            <input type="text" value="{$goodsInfo.goods_sn}" name="goods_sn"
                                                   class="form-control" style="width:350px;"/>
                                            <span id="err_goods_sn" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品分类:</td>
                                        <td>
                                            <div class="col-xs-3">
                                                <select name="cat_id" id="cat_id"
                                                        onchange="get_category(this.value,'cat_id_2','0');"
                                                        class="form-control" style=" margin-left:-15px;">
                                                    <option value="0">请选择商品分类</option>
                                                    <foreach name="cat_list" item="v" key="k">
                                                        <option value="{$v['id']}"
                                                        <if condition="$v['id'] eq $level_cat['1']">
                                                            selected="selected"
                                                        </if>
                                                        >
                                                        {$v['name']}
                                                        </option>
                                                    </foreach>
                                                </select>
                                            </div>
                                            <div class="col-xs-3">
                                                <select name="cat_id_2" id="cat_id_2"
                                                        onchange="get_category(this.value,'cat_id_3','0');"
                                                        class="form-control" style=" margin-left:-15px;">
                                                    <option value="0">请选择商品分类</option>
                                                </select>
                                            </div>
                                            <div class="col-xs-3">
                                                <select name="cat_id_3" id="cat_id_3" class="form-control"
                                                        style=" margin-left:-15px;">
                                                    <option value="0">请选择商品分类</option>
                                                </select>
                                            </div>
                                            <span id="err_cat_id" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>扩展分类:</td>
                                        <td>
                                            <div class="col-xs-3">
                                                <select name="extend_cat_id" id="extend_cat_id"
                                                        onchange="get_category(this.value,'extend_cat_id_2','0');"
                                                        class="form-control" style=" margin-left:-15px;">
                                                    <option value="0">请选择商品分类</option>
                                                    <foreach name="cat_list" item="v" key="k">
                                                        <option value="{$v['id']}"
                                                        <if condition="$v['id'] eq $level_cat2['1']">
                                                            selected="selected"
                                                        </if>
                                                        >
                                                        {$v['name']}
                                                        </option>
                                                    </foreach>
                                                </select>
                                            </div>
                                            <div class="col-xs-3">
                                                <select name="extend_cat_id_2" id="extend_cat_id_2"
                                                        onchange="get_category(this.value,'extend_cat_id_3','0');"
                                                        class="form-control" style=" margin-left:-15px;">
                                                    <option value="0">请选择商品分类</option>
                                                </select>
                                            </div>
                                            <div class="col-xs-3">
                                                <select name="extend_cat_id_3" id="extend_cat_id_3" class="form-control"
                                                        style=" margin-left:-15px;">
                                                    <option value="0">请选择商品分类</option>
                                                </select>
                                            </div>
                                            <span id="err_cat_id" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品品牌:</td>
                                        <td>
                                            <select name="brand_id" id="brand_id" class="form-control" style=" ">
                                                <option value="">所有品牌</option>
                                                <foreach name="brandList" item="v" key="k">
                                                    <option value="{$v['id']}"
                                                    <if condition="$v['id'] eq $goodsInfo['brand_id'] ">
                                                        selected="selected"
                                                    </if>
                                                    >
                                                    {$v['name']}
                                                    </option>
                                                </foreach>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>供应商:</td>
                                        <td>
                                            <select name="suppliers_id" id="suppliers_id" class="form-control"
                                                    style=" ">
                                                <option value="0">不指定供应商属于本店商品</option>
                                                <foreach name="suppliersList" item="v" key="k">
                                                    <option value="{$v['suppliers_id']}"
                                                    <if condition="$v['suppliers_id'] eq $goodsInfo['suppliers_id'] ">
                                                        selected="selected"
                                                    </if>
                                                    >
                                                    {$v['suppliers_name']}
                                                    </option>
                                                </foreach>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>本店售价:</td>
                                        <td>
                                            <input type="text" value="{$goodsInfo.shop_price}" name="shop_price"
                                                   class="form-control" style="width:150px;"
                                                   onkeyup="this.value=this.value.replace(/[^\d.]/g,'')"
                                                   onpaste="this.value=this.value.replace(/[^\d.]/g,'')"/>
                                            <span id="err_shop_price" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>市场价:</td>
                                        <td>
                                            <input type="text" value="{$goodsInfo.market_price}" name="market_price"
                                                   class="form-control" style="width:150px;"
                                                   onkeyup="this.value=this.value.replace(/[^\d.]/g,'')"
                                                   onpaste="this.value=this.value.replace(/[^\d.]/g,'')"/>
                                            <span id="err_market_price" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>成本价:</td>
                                        <td>
                                            <input type="text" value="{$goodsInfo.cost_price}" name="cost_price"
                                                   class="form-control" style="width:150px;"
                                                   onkeyup="this.value=this.value.replace(/[^\d.]/g,'')"
                                                   onpaste="this.value=this.value.replace(/[^\d.]/g,'')"/>
                                            <span id="err_cost_price" style="color:#F00; display:none"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>佣金:</td>
                                        <td>
                                            <input type="text" value="{$goodsInfo.commission}" name="commission"
                                                   class="form-control" style="width:150px;"
                                                   onkeyup="this.value=this.value.replace(/[^\d.]/g,'')"
                                                   onpaste="this.value=this.value.replace(/[^\d.]/g,'')"/> 用于分销的分成金额
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>上传商品图片:</td>
                                        <td>
                                            <input type="button" class="btn btn-default" value="上传图片"
                                                   onclick="GetUploadify(1,'','goods','call_back');"/>
                                            <input type="text" class="input-sm" name="original_img" id="original_img"
                                                   value="{$goodsInfo.original_img}"/>
                                            <if condition="$goodsInfo['original_img'] neq null">
                                                &nbsp;&nbsp;
                                                <a target="_blank" href="{$goodsInfo.original_img}" id="original_img2">
                                                    <img width="25" height="25"
                                                         src="{$config_siteurl}statics/extres/shop/images/image_icon.jpg">
                                                </a>
                                            </if>
                                            <span id="err_original_img" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品重量:</td>
                                        <td>
                                            <input type="text" class="form-control" style="width:150px;"
                                                   value="{$goodsInfo.weight}" name="weight"
                                                   onkeyup="this.value=this.value.replace(/[^\d.]/g,'')"
                                                   onpaste="this.value=this.value.replace(/[^\d.]/g,'')"/> &nbsp;克
                                            (以克为单位)
                                            <span id="err_weight" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>是否包邮:</td>
                                        <td>
                                            是:<input type="radio"
                                            <if condition="$goodsInfo[is_free_shipping] eq 1">checked="checked"</if>
                                            value="1" name="is_free_shipping" /> 否:
                                            <input type="radio"
                                            <if condition="$goodsInfo[is_free_shipping] eq 0">checked="checked"</if>
                                            value="0" name="is_free_shipping" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>库存数量:</td>
                                        <td>
                                            <if condition="$goodsInfo[goods_id] gt 0">
                                                <input type="text" value="{$goodsInfo.store_count}" class="form-control"
                                                       style="width:150px;" name="store_count"
                                                       onkeyup="this.value=this.value.replace(/[^\d.]/g,'')"
                                                       onpaste="this.value=this.value.replace(/[^\d.]/g,'')"/>
                                                <else/>
                                                <input type="text" value="{$tpshop_config[basic_default_storage]}"
                                                       class="form-control" style="width:150px;" name="store_count"
                                                       onkeyup="this.value=this.value.replace(/[^\d.]/g,'')"
                                                       onpaste="this.value=this.value.replace(/[^\d.]/g,'')"/>
                                            </if>

                                            <span id="err_store_count" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>赠送积分:</td>
                                        <td>
                                            <input type="text" class="form-control" style="width:150px;"
                                                   value="{$goodsInfo.give_integral}" name="give_integral"
                                                   onkeyup="this.value=this.value.replace(/[^\d.]/g,'')"
                                                   onpaste="this.value=this.value.replace(/[^\d.]/g,'')"/>
                                            <span id="err_give_integral" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>兑换积分:</td>
                                        <td>
                                            <input type="text" class="form-control" style="width:150px;"
                                                   value="{$goodsInfo.exchange_integral}" name="exchange_integral"
                                                   onkeyup="this.value=this.value.replace(/[^\d.]/g,'')"
                                                   onpaste="this.value=this.value.replace(/[^\d.]/g,'')"/>
                                            <span id="err_exchange_integral" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品关键词:</td>
                                        <td>
                                            <input type="text" class="form-control" style="width:550px;"
                                                   value="{$goodsInfo.keywords}" name="keywords"/>用空格分隔
                                            <span id="err_keywords" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品详情描述:</td>
                                        <td width="85%">
                                            <textarea class="span12 ckeditor" id="goods_content" name="goods_content"
                                                      title="">{$goodsInfo.goods_content}</textarea>
                                            <span id="err_goods_content" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--其他信息-->

                            <!-- 商品相册-->
                            <div class="tab-pane" id="tab_goods_images">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <foreach name="goodsImages" item="vo" key="k">
                                                <div style="width:100px; text-align:center; margin: 5px; display:inline-block;"
                                                     class="goods_xc">
                                                    <input type="hidden" value="{$vo['image_url']}"
                                                           name="goods_images[]">
                                                    <a onclick="" href="{$vo['image_url']}" target="_blank"><img
                                                                width="100" height="100" src="{$vo['image_url']}"></a>
                                                    <br>
                                                    <a href="javascript:void(0)"
                                                       onclick="ClearPicArr2(this,'{$vo['image_url']}')">删除</a>
                                                </div>
                                            </foreach>

                                            <div class="goods_xc"
                                                 style="width:100px; text-align:center; margin: 5px; display:inline-block;">
                                                <input type="hidden" name="goods_images[]" value=""/>
                                                <a href="javascript:void(0);"
                                                   onclick="GetUploadify(10,'','goods','call_back2');"><img
                                                            src="{$config_siteurl}statics/extres/shop/images/add-button.jpg"
                                                            width="100" height="100"/></a>
                                                <br/>
                                                <a href="javascript:void(0)">&nbsp;&nbsp;</a>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--商品相册-->

                            <!-- 商品规格-->
                            <div class="tab-pane" id="tab_goods_spec">
                                <table class="table table-bordered" id="goods_spec_table">
                                    <tr>
                                        <td>商品类型:</td>
                                        <td>
                                            <select name="spec_type" id="spec_type" class="form-control" style=" ">
                                                <option value="0">选择商品类型</option>
                                                <foreach name="goodsType" item="vo" key="k">
                                                    <option value="{$vo.id}"
                                                    <if condition="$goodsInfo[spec_type] eq $vo[id]">
                                                        selected="selected"
                                                    </if>
                                                    >{$vo.name}</option>
                                                </foreach>
                                            </select>
                                        </td>
                                    </tr>

                                </table>
                                <div id="ajax_spec_data">
                                    <!-- ajax 返回规格-->
                                </div>
                            </div>
                            <!-- 商品规格-->

                            <!-- 商品属性-->
                            <div class="tab-pane" id="tab_goods_attr">
                                <table class="table table-bordered" id="goods_attr_table">
                                    <tr>
                                        <td>商品属性:</td>
                                        <td>
                                            <select name="goods_type" id="goods_type" class="form-control" style=" ">
                                                <option value="0">选择商品属性</option>
                                                <foreach name="goodsType" item="vo" key="k">
                                                    <option value="{$vo.id}"
                                                    <if condition="$goodsInfo[goods_type] eq $vo[id]">
                                                        selected="selected"
                                                    </if>
                                                    >{$vo.name}</option>
                                                </foreach>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <!-- 商品属性-->
                        </div>
                        <div class="pull-right">
                            <input type="hidden" name="goods_id" value="{$goodsInfo.goods_id}">
                            <button class="btn btn-primary" @click="saveBtn" title="" data-toggle="tooltip"
                                    type="button"
                                    data-original-title="保存">保存
                            </button>
                        </div>
                    </form>
                    <!--表单数据-->
                </div>
            </div>
        </div>
        <!-- /.content -->
    </section>
</div>
<include file="Public/vue"/>
<script>

    $(document).ready(function () {
        new Vue({
            el: '#app',
            data: {},
            mixins: [window.__baseMethods],
            methods: {
                saveBtn: function () {
                    $.ajax({
                        url: "{:U('Goods/addEditGoods?is_ajax=1')}",
                        data: $('#addEditGoodsForm').serialize(),
                        type: "POST",
                        dataType: 'json',
                        success: function (res) {
                            console.log(res)
                            if (res.status) {
                                //添加成功
                                layer.msg('添加成功')
                                setTimeout(function () {
                                    location.href = "{:U('Shop/Goods/index')}"
                                }, 1500)
                            } else {
                                layer.msg('系统繁忙，请稍后')
                            }
                        }
                    })
                }
            }
        })
    })
    /*
     * 以下是图片上传方法
     */
    // 上传商品图片成功回调函数
    function call_back(fileurl_tmp) {
        $("#original_img").val(fileurl_tmp);
        $("#original_img2").attr('href', fileurl_tmp);
    }

    // 上传商品相册回调函数
    function call_back2(paths) {

        var last_div = $(".goods_xc:last").prop("outerHTML");
        for (i = 0; i < paths.length; i++) {
            $(".goods_xc:eq(0)").before(last_div); // 插入一个 新图片
            $(".goods_xc:eq(0)").find('a:eq(0)').attr('href', paths[i]).attr('onclick', '').attr('target', "_blank"); // 修改他的链接地址
            $(".goods_xc:eq(0)").find('img').attr('src', paths[i]); // 修改他的图片路径
            $(".goods_xc:eq(0)").find('a:eq(1)').attr('onclick', "ClearPicArr2(this,'" + paths[i] + "')").text('删除');
            $(".goods_xc:eq(0)").find('input').val(paths[i]); // 设置隐藏域 要提交的值
        }
    }
    /*
     * 上传之后删除组图input     
     * @access   public
     * @val      string  删除的图片input
     */
    function ClearPicArr2(obj, path) {
        $.ajax({
            type: 'GET',
            url: "{:U('Shop/Uploadify/delupload')}",
            data: {
                action: "del",
                filename: path
            },
            success: function () {
                $(obj).parent().remove(); // 删除完服务器的, 再删除 html上的图片				 
            }
        });
        // 删除数据库记录
        $.ajax({
            type: 'POST',
            url: "{:U('Goods/del_goods_images')}",
            data: {
                filename: path
            },
            success: function () {
                //		 
            }
        });
    }


    /** 以下 商品属性相关 [暂时关闭属性] js*/
    $(document).ready(function () {

        // 商品类型切换时 ajax 调用  返回不同的属性输入框
        $("#goods_type").change(function () {
            var goods_id = $("input[name='goods_id']").val();
            var type_id = $(this).val();
            $.ajax({
                type: 'GET',
                data: {
                    goods_id: goods_id,
                    type_id: type_id
                },
                url: "{:U('Goods/ajaxGetAttrInput')}",
                success: function (data) {
                    $("#goods_attr_table tr:gt(0)").remove()
                    $("#goods_attr_table").append(data);
                }
            });
        });
        // 触发商品类型
        $("#goods_type").trigger('change');
        $("input[name='exchange_integral']").blur(function () {
            var shop_price = parseInt($("input[name='shop_price']").val());
            var exchange_integral = parseInt($(this).val());
            if (shop_price * 100 < exchange_integral) {

            }
        });
    });


    // 属性输入框的加减事件
    function addAttr(a) {
        var attr = $(a).parent().parent().prop("outerHTML");
        attr = attr.replace('addAttr', 'delAttr').replace('+', '-');
        $(a).parent().parent().after(attr);
    }
    // 属性输入框的加减事件
    function delAttr(a) {
        $(a).parent().parent().remove();
    }


    /** 以下 商品规格相关 js*/
    $(document).ready(function () {

        // 商品类型切换时 ajax 调用  返回不同的属性输入框
        $("#spec_type").change(function () {
            var goods_id = '{$goodsInfo.goods_id}';
            var spec_type = $(this).val();
            $.ajax({
                type: 'GET',
                data: {
                    goods_id: goods_id,
                    spec_type: spec_type
                },
                url: "{:U('Goods/ajaxGetSpecSelect')}",
                success: function (data) {
                    $("#ajax_spec_data").html('')
                    $("#ajax_spec_data").append(data);
                    //alert('132');
                    ajaxGetSpecInput(); // 触发完  马上触发 规格输入框
                }
            });
        });
        // 触发商品规格
        $("#spec_type").trigger('change');
    });
    /** 以下是编辑时默认选中某个商品分类*/

    $(document).ready(function () {

        <?php if($level_cat['2'] > 0 ){?>
        // 商品分类第二个下拉菜单
        get_category('<?=$level_cat[1]?>', 'cat_id_2', '<?=$level_cat[2]?>');
        <?php } ?>

        <?php if($level_cat['3'] > 0 ){?>
        // 商品分类第二个下拉菜单
        get_category('<?=$level_cat[2]?>', 'cat_id_3', '<?=$level_cat[3]?>');
        <?php } ?>

        //  扩展分类
        <?php if($level_cat2['2'] > 0 ){?>
        // 商品分类第二个下拉菜单
        get_category('<?=$level_cat2[1]?>', 'extend_cat_id_2', '<?=$level_cat2[2]?>');
        <?php } ?>

        <?php if($level_cat2['3'] > 0 ){?>
        // 商品分类第二个下拉菜单
        get_category('<?=$level_cat2[2]?>', 'extend_cat_id_3', '<?=$level_cat2[3]?>');
        <?php } ?>

    });
</script>
</body>

</html>