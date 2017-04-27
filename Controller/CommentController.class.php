<?php

// +----------------------------------------------------------------------
// | 商品类型管理
// +----------------------------------------------------------------------

namespace Shop\Controller;
use Common\Controller\AdminBase;
use Shop\Service\GoodsService;
use Shop\Util\AjaxPage;

class CommentController extends AdminBase {
    public function index(){
        if (IS_POST) {
            $data = $this->getList();
            $this->ajaxReturn($data);
        }
        $this->display();
    }

    public function getList(){
        $model = M('ShopComment');

        $username = I('username','','trim');
        $content = I('content','','trim');
        $where=' parent_id = 0';
        if($username){
            $where .= " AND username like '%{$username}%'";
        }
        if($content){
            $where .= " AND content like '%{$content}%'";
        }        
        $count = $model->where($where)->count();
        $page = I('page',1);
        $limit = I('limit',10);
        $page_count = ceil ($count / $limit);
        $pageArr = array(
            'page' => $page,
            'page_count' => $page_count,
        );

                
        $comment_list = $model->where($where)->order('add_time DESC')->page($page,$limit)->select();
        if(!empty($comment_list))
        {
            $goods_id_arr = get_arr_column($comment_list, 'goods_id');
            $goods_list = M(GoodsService::GOODS_TABLE_NAME)->where("goods_id in (".  implode(',', $goods_id_arr).")")->getField("goods_id,goods_name");
        }

        return ['goods_list'=>$goods_list,'comment_list'=>$comment_list, 'page'=>$pageArr];
    } 
    
    public function detail(){
        $id = I('id');
        if(IS_POST) {
            $res = M('ShopComment')->where(array('comment_id' => $id))->find();
            if (!$res) {
                $this->ajaxReturn(['msg'=>'不存在该评论','status'=>false]);
            }

            $reply = M('ShopComment')->where(array('parent_id' => $id))->select(); // 评论回复列表

            $this->ajaxReturn(['comment'=>$res,'reply'=>$reply,'status'=>true]);
        }
        $this->assign('id',$id);
        $this->display();
    }
    public function doReply(){
        if(IS_POST){
            $id = I('id');
            $res = M('ShopComment')->where(array('comment_id'=>$id))->find();
            $add['parent_id'] = $id;
            $add['content'] = I('post.content');
            $add['goods_id'] = $res['goods_id'];
            $add['add_time'] = time();
            $add['username'] = 'admin';

            $add['is_show'] = 1;

            $row =  M('ShopComment')->add($add);
            if($row){
                $this->ajaxReturn(['msg'=>'添加成功','icon'=>1]);
            }
            $this->ajaxReturn(['msg'=>'添加失败','icon'=>2]);

        }
    }

    public function del(){
        $id = I('get.id');
        $row = M('ShopComment')->where(array('comment_id'=>$id))->delete();
        if($row){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
    public function ask_list(){
        if (IS_POST) {
            $data = $this->get_ask_list();
            $this->ajaxReturn($data);
        }
    	$this->display();
    }
      public function get_ask_list(){
    	$model = M('goods_consult');
    	$username = I('username','','trim');
    	$content = I('content','','trim');
    	$where=' parent_id = 0';
    	if($username){
    		$where .= " AND username like '%{$username}%'";
    	}
    	if($content){
    		$where .= " AND content like '%{$content}%'";
    	}
        $count = $model->where($where)->count();
          $page = I('page',1);
          $limit = I('limit',10);
          $page_count = ceil ($count / $limit);
          $pageArr = array(
              'page' => $page,
              'page_count' => $page_count,
          );

    	
        $comment_list = $model->where($where)->order('add_time DESC')->page($page,$limit)->select();
    	if(!empty($comment_list))
    	{
    		$goods_id_arr = get_arr_column($comment_list, 'goods_id');
    		$goods_list = M(GoodsService::GOODS_TABLE_NAME)->where("goods_id in (".  implode(',', $goods_id_arr).")")->getField("goods_id,goods_name");
    	}
    	$consult_type = array(0=>'默认咨询',1=>'商品咨询',2=>'支付咨询',3=>'配送',4=>'售后');

          return ['consult_type'=>$consult_type,'goods_list'=>$goods_list,'comment_list'=>$comment_list,'page'=>$pageArr];

    }
     
    public function consult_info(){
        $id = I('id');
        if(IS_POST) {
            $res = M('goods_consult')->where(array('id' => $id))->find();
            if (!$res) {
                $this->ajaxReturn(['msg'=>'不存在该咨询','status'=>false]);
            }

            $reply = M('goods_consult')->where(array('parent_id' => $id))->select(); // 咨询回复列表
            $this->assign('comment', $res);
            $this->assign('reply', $reply);
            $this->ajaxReturn(['comment'=>$res,'reply'=>$reply,'status'=>true]);
        }
        $this->assign('id',$id);
    	$this->display();
    }
    public function addConsult(){
        if(IS_POST){
            $id = I('id');
            $res = M('goods_consult')->where(array('id' => $id))->find();
            $add['parent_id'] = $id;
            $add['content'] = I('post.content');
            $add['goods_id'] = $res['goods_id'];
            $add['consult_type'] = $res['consult_type'];
            $add['add_time'] = time();
            $add['username'] = 'admin';
            $add['is_show'] = 1;
            $row =  M('goodsConsult')->add($add);

            if($row){
                $this->ajaxReturn(['msg'=>'添加成功','icon'=>1]);
            }
            $this->ajaxReturn(['msg'=>'添加失败','icon'=>2]);
        }
    }

     public function ask_handle(){
    	$type = I('post.type');
    	$selected_id = I('post.selected');        
    	if(!in_array($type,array('del','show','hide')) || !$selected_id)
    		$this->error('操作完成');
    
        $selected_id = implode(',',$selected_id);
    	if($type == 'del'){
    		//删除咨询
    		$where .= "( id IN ({$selected_id}) OR parent_id IN ({$selected_id})) ";
    		$row = M('goods_consult')->where($where)->delete();
    	}
    	if($type == 'show'){
    		$row = M('goods_consult')->where("id IN ({$selected_id})")->save(array('is_show'=>1));
    	}
    	if($type == 'hide'){
    		$row = M('goods_consult')->where("id IN ({$selected_id})")->save(array('is_show'=>0));
    	}    		
    	$this->success('操作完成');
    }

    public function delAsk(){
        $id = I('get.id');
        $row = M('goodsConsult')->where(array('id'=>$id))->delete();
        if($row){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}