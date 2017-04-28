<?php

// +----------------------------------------------------------------------
// | 商品类型管理
// +----------------------------------------------------------------------

namespace Shop\Controller;

use Common\Controller\AdminBase;
use Shop\Service\GoodsService;
use Shop\Util\Page;

class TypeController extends AdminBase {
    /**
     * 商品类型  用于设置商品的属性
     */
    public function index() {
        if (IS_AJAX) {
            $model = M(GoodsService::GOODS_TYPE_TABLE_NAME);
            $count = $model->count();

            $page = I('page', 1);
            $limit = I('limit', 20);
            $page_count = ceil($count / $limit);
            $pageArr = array(
                'page' => $page,
                'page_count' => $page_count,
            );
            $res = $model->order("id desc")->page($page, $limit)->select();
            if ($res) {
                $this->ajaxReturn(['status' => true, 'data' => $res, 'page' => $pageArr]);
            }
            $this->ajaxReturn(['status' => false, 'data' => [], 'page' => $pageArr]);
        }
        $this->display('index');
    }

    /**
     * 添加修改编辑  商品属性类型
     */
    public function addEditGoodsType() {
        if (IS_POST) {
            $id = I('id', 0);
            $post = I('post.');
            $model = M(GoodsService::GOODS_TYPE_TABLE_NAME);

            if ($id == '') {
                $model->add($post);
                $this->ajaxReturn(['data' => $post, 'status' => true]);
            } else {
                unset($post['id']);
                $res = $model->where(['id' => $id])->save($post);
                $this->ajaxReturn(['data' => $res, 'status' => true]);
            }
        }

    }

    public function getTypeDetail() {
        $id = I('id', 0);
        if (IS_AJAX) {
            $res = M(GoodsService::GOODS_TYPE_TABLE_NAME)->find($id);
            if ($res) {
                $this->ajaxReturn(['data' => $res, 'status' => true]);
            } else {
                $this->ajaxReturn(['data' => ['name' => '', 'id' => $id], 'status' => false]);
            }
        }
        $this->assign('id', $id);
        $this->display('goods_type');
    }

    /**
     * 删除商品类型
     */
    public function delGoodsType() {
        $id = I('post.id', 0);
        // 判断 商品规格        
        $count = M("ShopSpec")->where(['type_id' => $id])->count("1");
        $count > 0 && $this->error('该类型下有商品规格不得删除!', U('Type/index'));
        // 判断 商品属性        
        $count = M("GoodsAttribute")->where(['type_id' => $id])->count("1");
        $count > 0 && $this->error('该类型下有商品属性不得删除!', U('Type/index'));
        // 删除分类
        M(GoodsService::GOODS_TYPE_TABLE_NAME)->where(['id' => $id])->delete();
        $this->success("操作成功", U('Type/index'));
    }
}