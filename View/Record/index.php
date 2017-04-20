<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed" style="padding:10px;">
<style>
    td p {
        font-size: 12px;
        color: #999999;
    }
</style>
<div id="app">
    <div class="h_a">搜索</div>
    <div>
        <div class="search_type cc mb10">
            订单号：<input type="text" v-model="where.target" name="" class="input">
            发送者：<input type="text" v-model="where.from" name="" class="input">
            接收者：<input type="text" v-model="where.to" name="" class="input">
            状态：<select v-model="where.status" name="" id="">
                <option value="">全部</option>
                <option value="0">正常</option>
                <option value="1">无效</option>
                <option value="2">冻结</option>
            </select>
            <button @click="getList" class="btn">搜索</button>
        </div>
    </div>
    <form class="J_ajaxForm" action="" method="post">
        <div class="table_list">
            <table width="100%">
                <thead>
                <tr>
                    <td width="100" align="center">id</td>
                    <td width="120" align="center">订单号</td>
                    <td width="120" align="center">发送者</td>
                    <td width="120" align="center">接收者</td>
                    <td align="center">内容</td>
                    <td width="100" align="center">收入</td>
                    <td width="100" align="center">支出</td>
                    <td width="100" align="center">余额</td>
                    <td width="140" align="center">创建时间</td>
                    <td width="140" align="center">更新时间</td>
                    <td width="100" align="center">状态</td>
                </tr>
                </thead>
                <tr v-for="item in lists">
                    <td align="center">
                        {{ item.id }}
                    </td>
                    <td align="center">
                        {{ item.target }}
                        <p>{{ item.target_type }}</p>
                    </td>
                    <td align="center">
                        {{ item.from }}
                        <p>
                            {{ item.from_type }}
                        </p>
                    </td>
                    <td align="center">
                        {{ item.to }}
                        <p>
                            {{ item.to_type }}
                        </p>
                    </td>
                    <td align="center">{{ item.detail }}</td>
                    <td align="center">{{ item.income }}</td>
                    <td align="center">{{ item.pay }}</td>
                    <td align="center">{{ item.balance }}</td>
                    <td align="center">
                        <p>{{ item.create_time | getFormatTime }}</p>
                    </td>
                    <td align="center">
                        <p>{{ item.update_time | getFormatTime }}</p>
                    </td>
                    <td align="center" v-html="getStatus(item.status)">
                    </td>
                </tr>
            </table>
            <div v-if="page_count > 1" style="text-align: center">
                <ul class="pagination pagination-sm no-margin">
                    <li>
                        <a @click="page > 1 ? (page--) : '' ;getList()" href="javascript:;">上一页</a>
                    </li>
                    <li>
                        <a href="javascript:;">{{ page }} / {{ page_count }}</a>
                    </li>
                    <li><a @click="page<page_count ? page++ : '' ;getList()" href="javascript:;">下一页</a></li>
                    <input type="number" min="1" :max="page_count" v-model="temp_page" style="width:50px;margin-left: 50px;;margin-right: 5px"><a class="btn btn-info" @click="toPage()" href="javascript:;">GO</a>
                </ul>
            </div>
        </div>
    </form>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
<script src="//cdn.bootcss.com/vue/2.2.6/vue.js"></script>
<script>
    $(document).ready(function () {
        new Vue({
            el: '#app',
            data: {
                lists: [],
                page: 1,
                temp_page: 1,
                limit: 10,
                page_count: 0,
                total: 0,
                where: {
                    status: ''
                }
            },
            filters: {
                getFormatTime: function (value) {
                    var time = new Date(parseInt(value * 1000));
                    var y = time.getFullYear();
                    var m = time.getMonth() + 1;
                    var d = time.getDate();
                    var h = time.getHours();
                    var i = time.getMinutes();
                    var res = y + '-' + (m < 10 ? '0' + m : m) + '-' + (d < 10 ? '0' + d : d)
                    res += '  ' + (h < 10 ? '0' + h : h) + ':' + (i < 10 ? '0' + i : i);
                    return res;
                }
            },
            methods: {
                getList: function () {
                    var that = this
                    var where = {
                        tabName: '{$tabName}',
                        page: this.page,
                        limit: this.limit,
                        where: this.where
                    }
                    $.ajax({
                        url: "{:U('index')}",
                        data: where,
                        dataType: 'json',
                        type: 'get',
                        success: function (res) {
                            console.log(res)
                            var data = res.info
                            that.lists = data.lists
                            that.page = data.page
                            that.temp_page = data.page
                            that.limit = data.limit
                            that.page_count = data.page_count
                        }
                    })
                },
                getStatus: function(value){
                    var name = '';
                    switch (value){
                        case '0':
                            name = '<span class="label label-success">正常</span>';
                            break;
                        case '1':
                            name = '<span class="label label-danger">无效</span>';
                            break;
                        case '2':
                            name = '<span class="label label-info">冻结</span>';
                            break;
                    }
                    return name;
                },
                toPage: function () {
                    if (this.temp_page < 1)  this.temp_page = 1;
                    if (this.temp_page > this.page_count)  this.temp_page = this.page_count;

                    this.page = this.temp_page;
                    this.getList();
                }
            },
            mounted: function () {
                this.getList();
            }
        })
    })
</script>
</body>
</html>