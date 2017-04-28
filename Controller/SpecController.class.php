<?php

// +----------------------------------------------------------------------
// | 商品类型管理
// +----------------------------------------------------------------------

namespace Shop\Controller;

use Common\Controller\AdminBase;
use Shop\Service\GoodsService;
use Shop\Util\Page;
use Shop\Logic\GoodsLogic;


class SpecController extends AdminBase {
    /**
     * 商品类型  用于设置商品的属性
     */
    public function index() {
        if (IS_AJAX) {
            $data = $this->getSpecList();
            $this->ajaxReturn($data);
        }

        $this->display();
    }

    /**
     * 添加修改编辑  商品属性类型
     */
    public function addEditGoodsType() {
        $_GET['id'] = $_GET['id'] ? $_GET['id'] : 0;
        $model = M(GoodsService::GOODS_TYPE_TABLE_NAME);
        if (IS_POST) {
            $model->create();
            if ($_GET['id']) {
                $model->save();
            } else {
                $model->add();
            }

            $this->success("操作成功", U('Type/index'));
            exit;
        }
        $goodsType = $model->find($_GET['id']);
        $this->assign('goodsType', $goodsType);
        $this->display('goods_type');
    }

    /**
     * 删除商品类型
     */
    public function delGoodsType() {
        // 判断 商品规格        
        $count = M("Spec")->where("type_id = {$_GET['id']}")->count("1");
        $count > 0 && $this->error('该类型下有商品规格不得删除!', U('Type/index'));
        // 判断 商品属性        
        $count = M("GoodsAttribute")->where("type_id = {$_GET['id']}")->count("1");
        $count > 0 && $this->error('该类型下有商品属性不得删除!', U('Type/index'));
        // 删除分类
        M(GoodsService::GOODS_TYPE_TABLE_NAME)->where("id = {$_GET['id']}")->delete();
        $this->success("操作成功", U('Type/index'));
    }

    /**
     *  商品规格列表
     */
    public function getSpecList() {

        $where = ' 1 = 1 '; // 搜索条件                        
        I('type_id') && $where = "$where and type_id = " . I('type_id');
        // 关键词搜索               
        $model = D('spec');
        $count = $model->where($where)->count();
        $page = I('page', 1);
        $limit = I('limit', 20);
        $page_count = ceil($count / $limit);
        $pageArr = array(
            'page' => $page,
            'page_count' => $page_count,
        );

        $specList = $model->where($where)->order('`order` desc')->page($page, $limit)->select();
        $GoodsService = new GoodsService();
        foreach ($specList as $k => $v) {       // 获取规格项
            $arr = $GoodsService->getSpecItem($v['id']);
            $specList[$k]['spec_item'] = implode(' , ', $arr);
        }


        $goodsTypeList = M(GoodsService::GOODS_TYPE_TABLE_NAME)->select(); // 规格分类
        $goodsTypeList = convert_arr_key($goodsTypeList, 'id');

        return ['goodsTypeList' => $goodsTypeList, 'specList' => $specList, 'page' => $pageArr];
    }

    /**
     * 添加修改编辑  商品规格
     */
    public function addEditSpec() {
        if (IS_POST) {
            $post = I('post.');

            if ($post['detail']['type_id'] == '') {
                $this->ajaxReturn(['msg' => '没有选择商品类型', 'status' => false]);
            }

            $items = $post['items'];
            $itemsArr = explode('，', $items);
            $model = M('Spec');
            $id = I('id', 0);
            if ($id == 0) {
                // 添加
                $res = $model->add($post['detail']); // 插入id
                foreach ($itemsArr as $val) {
                    if ($val != '') {
                        M('specItem')->add(['spec_id' => $res, 'item' => $val]);
                    }
                }
            } else {
                // 修改
                $res = $model->where(['id' => $id])->save($post['detail']);
                M('specItem')->where(['spec_id' => $id])->delete();
                foreach ($itemsArr as $val) {
                    if ($val != '') {
                        M('specItem')->add(['spec_id' => $id, 'item' => $val]);
                    }
                }
            }
            $this->ajaxReturn(['data' => $res, 'status' => true]);
        }
    }

    public function getSpecDetail() {
        $id = I('id', 0);
        if (IS_AJAX) {
            $res = M("Spec")->find($id);
            $goodsType = M(GoodsService::GOODS_TYPE_TABLE_NAME)->select();
            if ($res) {
                $specItem = M('SpecItem')->where(['spec_id' => $id])->select();
                $specItemStr = '';
                foreach ($specItem as $val) {
                    $specItemStr .= $val['item'] . "
";
                }
                $specItemStr = rtrim($specItemStr);
                $this->ajaxReturn([
                    'specItemStr' => $specItemStr,
                    'goodsType' => $goodsType,
                    'data' => $res,
                    'status' => true
                ]);
            } else {
                $res = [
                    'type_id' => '',
                    'name' => '',
                    'order' => '',
                    'search_index' => ''
                ];
                $this->ajaxReturn(['goodsType' => $goodsType, 'data' => $res, 'status' => false]);
            }
        }
        $this->assign('id', $id);
        $this->display('spec');
    }

    /**
     * 删除商品规格
     */
    public function delGoodsSpec() {
        $id = I('post.id');
        // 判断 商品规格项
        $count = M("SpecItem")->where("spec_id = '%d'", $id)->count("1");
        $count > 0 && $this->error('清空规格项后才可以删除!', U('Spec/index'));
        // 删除分类
        M('Spec')->where("id = '%d'", $id)->delete();
        $this->success("删除成功", U('Spec/index'));
    }
}