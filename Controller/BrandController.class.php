<?php

// +----------------------------------------------------------------------
// | 商品类型管理
// +----------------------------------------------------------------------

namespace Shop\Controller;
use Common\Controller\AdminBase;
use Shop\Util\Page;

class BrandController extends AdminBase {
    /**
     * 品牌列表
     */
    public function index(){
        if (IS_POST) {
            $data = $this->getList();
            $this->ajaxReturn($data);
        }
        $this->display('index');
    }

    public function getList(){
        if (IS_POST) {
            $model = M("Brand");
            $where = "";
            $keyword = I('keyword');
            $where = $keyword ? " name like '%$keyword%' " : "";
            $count = $model->where($where)->count();
            $page = I('page',1);
            $limit = I('limit',10);
            $page_count = ceil ($count / $limit);
            $pageArr = array(
                'page' => $page,
                'page_count' => $page_count,
            );

            $brandList = $model->where($where)->order("`sort` asc")->page($page,$limit)->select();

            $cat_list = M('goods_category')->where("parent_id = 0")->getField('id,name'); // 已经改成联动菜单

            return ['cat_list'=>$cat_list,'brandList'=>$brandList,'page'=>$pageArr];
        }
    }
      /**
     * 添加修改编辑  商品品牌
     */
    public  function addEditBrand(){
        if(IS_POST) {
            $post = I('post.');
            $id = I('id');

            $model = M("Brand");

            if($id == ''){
                $res = $model->add($post['detail']);
            }else{
                unset($post['id']);
                $res = $model->where(['id'=>$id])->save($post['detail']);
            }
            $this->ajaxReturn(['data'=>$res,'status'=>true]);
        }

    }
    public function getBrandDetail(){
        $id = I('id');
        if (IS_POST){
            $model = M("Brand");
            $cat_list = M('goods_category')->where("parent_id = 0")->select(); // 已经改成联动菜单

            $brand = $model->find($id);
            if ($brand) {
                $this->ajaxReturn(['brand'=>$brand, 'cat_list'=>$cat_list,'status' => true]);
            }else{
                $brand = [
                    'name' => '',
                    'logo' => '',
                    'desc' => '',
                    'url' => '',
                    'sore' => '',
                    'parent_cat_id' => '',
                    'cat_id' => '',
                ];
                $this->ajaxReturn(['brand'=>$brand, 'cat_list'=>$cat_list,'status' => false]);
            }
        }
        $this->assign('id',$id);
        $this->display('add_edit_brand');
    }
     /**
     * 删除品牌
     */
    public function delBrand()
    {        
        // 判断此品牌是否有商品在使用
        $goods_count = M('Goods')->where("brand_id = {$_GET['id']}")->count('1');        
        if($goods_count)
        {
            $this->error('此品牌有商品在用不得删除!');
        }
        
        $model = M("Brand"); 
        $model->where('id ='.$_GET['id'])->delete();
        $this->success("操作成功");
    }     
}