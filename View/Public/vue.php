<script src="{$config_siteurl}statics/extres/shop/js/vue/vue.js"></script>
<script>
    window.config = {
        url: {
            goods_list: '/index.php?g=Shop&m=Goods&a=goodsList',
            goods_del: '/index.php?g=Shop&m=goods&a=delGoods'
        }
    }

    window.__baseMethods = {
        methods: {
            /**
             * 更新表中指定字段
             *
             * @param table 表名称
             * @param id_name  表主键名称
             * @param id_value 表主键的值
             * @param field 更新的字段
             * @param value 更新的值
             * @param obj  操作的对象，如需修改该对象值
             */
            changeTableVal: function (table, id_name, id_value, field, value, obj) {
                if (obj) {
                    obj[field] = value
                }
                $.ajax({
                    url: "/index.php?g=Shop&m=AdminApi&a=changeTableVal",
                    data: {table: table, id_name: id_name, id_value: id_value, field: field, value: value},
                    success: function (data) {
                        layer.msg('修改成功', {icon: 1, time: 1000});
                    }
                });
            },
            ajaxUpdateField: function (table, id, name, value) {
                $.ajax({
                    type: 'POST',
                    data: {table: table, id: id, field: name, value: value},
                    url: "index.php?g=Shop&m=Goods&a=updateField",
                    success: function (res) {
                        layer.msg('修改成功', {icon: 1, time: 1000});
                    }
                });
            }
        }
    }
</script>

<script>
    $(document).ready(function () {
        //注册 ajax加载时 显示加载框
        $(document).ajaxStart(function () {
            if (layer) {
                window.__layer_loading_index = layer.load(1);
            }
        });
        $(document).ajaxComplete(function () {
            if (layer) {
                layer.close(window.__layer_loading_index);
            }
        });
        $(document).ajaxError(function () {
            if (layer) {
                layer.msg('网络繁忙，请稍后再试..');
            }
        })

    });
</script>

<!-- 分页组件  -->

<script type="text/x-template" id="vPage">
    <div class="col-sm-12">
        <div class="dataTables_paginate paging_simple_numbers">
            <button class="btn btn-primary" v-on:click="preBtn">上一页</button>
            <div style="display: inline; font-size: 16px; margin-left: 10px; margin-right: 10px;">
                <span>{{page}}</span> / <span>{{page_count}}</span></div>
            <button class="btn btn-primary" v-on:click="nextBtn">下一页</button>
            <input type="text" v-model="goPage" placeholder="跳转页码" class="form-control input-sm"
                   style="width: 70px; display: inline;">
            <button @click="goPageBtn" class="btn btn-primary">GO</button>
        </div>
    </div>
</script>

<script>
    var pageComponent = {
        props: ['page', 'page_count'],
        template: '#vPage',
        data: function () {
            return {goPage: 1}
        },
        methods: {
            updateList: function () {
                var that = this
                var load = layer.load(1)
                setTimeout(function () {
                    that.$emit('update')
                    layer.close(load);
                }, 300)
            },
            preBtn: function () {
                console.log('xx')
                if (this.page > 1) {
                    this.$parent.page -= 1
                    this.updateList()
                } else {
                    layer.msg('当前已经是第一页')
                }
            },
            nextBtn: function () {
                if (this.page < this.page_count) {
                    this.$parent.page = parseInt(this.page) + 1
                    console.log(this.$parent.page)
                    this.updateList()
                } else {
                    layer.msg('当前已经是最后一页')
                }
            },
            goPageBtn: function () {
                if (this.goPage < this.page || this.goPage > this.page_count) {
                    layer.msg('超出页数范围')
                    this.goPage = 1
                } else {
                    this.$parent.page = this.goPage
                    this.updateList()
                }
            }
        }
    }
</script>