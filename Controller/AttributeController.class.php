<?php

// +----------------------------------------------------------------------
// | 商品属性管理
// +----------------------------------------------------------------------

namespace Shop\Controller;
use Common\Controller\AdminBase;
use Shop\Util\Page;
use Shop\Util\AjaxPage;


class AttributeController extends AdminBase {
     /**
     * 商品属性列表
     */
    public function index(){
        if (IS_POST) {
            $data = $this->getGoodsAttributeList();
            $this->ajaxReturn($data);
        }

        $this->display();
    }   
    
    /**
     *  商品属性列表
     */
    public function getGoodsAttributeList(){
        //ob_start('ob_gzhandler'); // 页面压缩输出
        $where = ' 1 = 1 '; // 搜索条件                        
        I('type_id')   && $where = "$where and type_id = ".I('type_id') ;                
        // 关键词搜索               
        $model = M('GoodsAttribute');
        $count = $model->where($where)->count();
        $Page       = new AjaxPage($count,13);

        $goodsAttributeList = $model->where($where)->order('`order` desc,attr_id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $goodsTypeList = M("GoodsType")->select(); // 分类
        $goodsTypeList = convert_arr_key($goodsTypeList, 'id');
        $attr_input_type = array(0=>'手工录入',1=>' 从列表中选择',2=>' 多行文本框');

        return ['attr_input_type'=>$attr_input_type,'goodsTypeList'=>$goodsTypeList,'goodsAttributeList'=>$goodsAttributeList];
    }  
     /**
     * 添加修改编辑  商品属性
     */
    public  function addEditGoodsAttribute(){
        if (IS_POST) {
            $post = I('post.');

            if($post['detail']['type_id'] == ''){
                $this->ajaxReturn(['msg'=>'没有选择商品类型','status'=>false]);
            }

            $model = M('goodsAttribute');
            $id = I('id',0);

            if ($id == 0) {
                // 添加
                $res = $model->add($post['detail']); // 插入id
            } else {
                // 修改
                $res = $model->where(['attr_id'=>$id])->save($post['detail']);
            }
            $this->ajaxReturn(['data'=>$res,'status'=>true]);

        }
    }

    public function getAttrDetail() {
        $id = I('id', 0);
        if (IS_POST) {
            $res = M("GoodsAttribute")->find($id);
            $goodsType = M("GoodsType")->select();
            if ($res) {
                $this->ajaxReturn(['goodsType'=>$goodsType,'data' => $res, 'status' => true]);
            } else {
                $res = [
                    'attr_name' => '',
                    'type_id' => '',
                    'attr_index' => '',
                    'attr_type' => '',
                    'attr_input_type' => '',
                    'attr_value' => '',
                    'order' => '',
                ];
                $this->ajaxReturn(['goodsType'=>$goodsType,'data' => $res, 'status' => false]);
            }
        }
        $this->assign('id',$id);
        $this->display('add_edit_goods_attribute');
    }
       /**
     * 删除商品属性
     */
    public function delGoodsAttribute()
    {         
        // 判断 有无商品使用该属性
        $count = M("GoodsAttr")->where("attr_id = {$_GET['id']}")->count("1");   
        $count > 0 && $this->error('有商品使用该属性,不得删除!',U('Attribute/index'));                        
        // 删除 属性
        M('GoodsAttribute')->where("attr_id = {$_GET['id']}")->delete();   
        $this->success("操作成功",U('Attribute/index'));
    }  
}