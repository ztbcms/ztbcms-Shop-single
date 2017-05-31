<?php
namespace Shop\Controller;

use Common\Controller\Base;
use Shop\Service\CategoryService;
use Shop\Service\GoodsService;

class GoodsApiController extends Base {
    /**
     * 获取商品详情
     */
    public function goods_info() {
        if(IS_GET){
            $goods_id = I('get.goods_id',0);
            if($goods_id == 0){
                $this->ajaxReturn(array('status'=>false, 'data'=>null, 'msg'=>'缺少商品id'));
            }
            $res = GoodsService::get_goods_info($goods_id);
            $this->ajaxReturn($res);

        }else{
            $this->ajaxReturn(array('status'=>false, 'data'=>null, 'msg'=>'请求方法错误'));
        }
    }

    /**
     *  获取商品列表
     */
    public function goods_list() {
        $where = [];
        if (I('get.is_recommend')) {
            $where['is_recommend'] = true;
        }
        if (I('get.is_new')) {
            $where['is_new'] = true;
        }
        if (I('get.is_hot')) {
            $where['is_hot'] = true;
        }
        $page = I('get.page', 1);
        $limit = I('get.limit', 20);

        $catid = I('get.catid', 0);
        $order = I('get.order', '');
        $onsale = I('get.is_on_sale','1');

        // 添加关键词搜索
        $key_word = I('get.key_word') ? trim(I('get.key_word')) : '';
        if($key_word){
            $search['goods_name'] = array('like','%'.$key_word.'%');
            $search['keywords'] = array('like','%'.$key_word.'%');
            $search['_logic'] = 'or';
            $where['_complex'] = $search;
        }

        $res = GoodsService::get_goods_list($where, $catid, $order, $onsale, $page, $limit);

        $this->ajaxReturn($res);

//        $goods_service = new GoodsService();
//        $goods_res = $goods_service->get_goods_list($where, $catid, $order, $onsale, $page, $limit);
//        $this->success($goods_res, '', true);
    }

    /**
     * 获取商品分类
     */
    public function getGoodsCat() {
        if (I('get.parent_id')) {
            $where['parent_id'] = I('get.parent_id');
        }
        $where['is_show'] = 1;
        $res = M(CategoryService::TABLE_NAME)->where($where)->select();
        if ($res) {
            $this->success($res ? $res : [], '', true);
        } else {
            $this->error();
        }
    }
}

