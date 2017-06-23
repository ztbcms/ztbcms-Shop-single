<include file="Public/min-header"/>
<div class="wrapper">
    <include file="Public/breadcrumb"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-list"></i> 评论回复
                        <a data-original-title="返回" class="btn btn-default pull-right" style="margin-top:-8px;" title="" data-toggle="tooltip" href="javascript:history.go(-1)"><i class="fa fa-reply"></i></a>
                    </h3>
                </div>
                <div class="panel-body" id="app">
                    <div class="row">
                    <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <!-- DIRECT CHAT PRIMARY -->
                            <div class="box direct-chat direct-chat-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">用户评论</h3>
                                    <!-- 
                                    <div class="box-tools pull-right">
                                        <span class="badge bg-light-blue" title="3 New Messages" data-toggle="tooltip">3</span>
                                        <button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
                                        <button data-widget="chat-pane-toggle" title="" data-toggle="tooltip" class="btn btn-box-tool" data-original-title="Contacts"><i class="fa fa-comments"></i></button>
                                        <button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
                                    </div>
                                     -->
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    <!-- Conversations are loaded here -->
                                    <div class="">
                                        <!-- Message. Default to the left -->
                                        <div class="direct-chat-msg">
                                            <div class="direct-chat-info clearfix">
                                                <span class="direct-chat-name pull-left"><!--用户名 --></span>
                                                <span class="direct-chat-timestamp pull-right">{{ comment.add_time | getFormatTime }}</span>
                                            </div><!-- /.direct-chat-info -->
                                            <img :alt="comment.username" src="{$config_siteurl}statics/extres/shop/dist/img/user2-160x160.jpg" class="direct-chat-img"><!-- /.direct-chat-img -->
                                            <div class="direct-chat-text">
                                                 {{comment.content}}
                                            </div><!-- /.direct-chat-text -->
                                        </div><!-- /.direct-chat-msg -->

                                        <div class="direct-chat-msg right" v-for="item in reply">
                                            <div class="direct-chat-info clearfix">
                                                <span class="direct-chat-name pull-right"><!--管理员 --></span>
                                                <span class="direct-chat-timestamp pull-left">{{ item.add_time | getFormatTime }}</span>
                                            </div><!-- /.direct-chat-info -->
                                            <img alt="管理员" src="{$config_siteurl}statics/extres/shop/dist/img/user2-160x160.jpg" class="direct-chat-img"><!-- /.direct-chat-img -->
                                            <div class="direct-chat-text">
                                                 {{item.content}}
                                            </div>
                                        </div>

                                    </div> 
                                    <!-- /.direct-chat-pane -->
                                </div><!-- /.box-body -->
                                <!-- /.box-footer-->
                            </div><!--/.direct-chat -->
                        </div>       
                        <div class="col-md-2"></div>
                     <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-md-8">
                                <form method="post" onsubmit="return false;">
                                    <textarea class="form-control" rows="3" placeholder="请输入回复内容" id="content"></textarea>
                                    <div class="form-group"><button type="submit" @click="doReply()" class="btn btn-primary pull-right margin">回复</button></div>
                                </form>            
                                </div>
                                <div class="col-md-2"></div></div>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<include file="Public/vue"/>
<script>
    new Vue({
        el: '#app',
        data:{
            comment: [],
            reply:[]
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
            getDetail: function(){
                var that = this;
                $.ajax({url: "{:U('Comment/detail')}", type: 'post', data: {'id': '<?php echo $id;?>'}, dataType: 'json',
                    success:function (res){
                        if(res.status){
                            that.comment = res.comment;
                            that.reply = res.reply;
                        }else{
                            layer.alert(res.msg,function(){
                                window.location.href = "{:U('Comment/index')}";
                            });
                        }
                    }
                });
            },
            doReply: function () {
                var that = this;
                var content = $('#content').val();
                if (content == '') {
                    layer.alert('没有输入内容');
                    return;
                }
                $.ajax({url: "{:U('Comment/doReply')}", type: 'post', data: {'id': '<?php echo $id;?>', 'content': content}, dataType: 'json',
                    success:function (res){
                        $('#content').val('');
                        layer.alert(res.msg,{
                            icon: res.icon
                        });
                        that.getDetail();
                    }
                });
            }
        },
        mounted: function(){
            this.getDetail();
        }
    });
</script>
</body>
</html>