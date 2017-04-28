<?php
namespace Shop\Controller;

use Common\Controller\Base;
use Shop\Service\CategoryService;
use Shop\Service\GoodsService;

class GoodsApiController extends Base {
    /**
     * @param $goods_id
     */
    public function goods_info($goods_id) {
        $goods = M('ShopGoods')->where("goods_id = $goods_id")->find();
        //将商品详情转义html
        $goods['goods_content'] = htmlspecialchars_decode($goods['goods_content']);
        if (empty($goods) || ($goods['is_on_sale'] == 0)) {
            $this->error('该商品已经下架', '', true);
        }
        $goods_images_list = M(GoodsService::GOODS_IMAGES_TABLE_NAME)->where("goods_id = '%d'", $goods_id)->select(); // 商品 图册
        $goods_attribute = M(GoodsService::GOODS_ATTRIBUTE_TABLE_NAME)->where("type_id='%d'",
            $goods['goods_type'])->getField('attr_id,attr_name'); // 查询属性
        $goods_attr_list = M(GoodsService::GOODS_ATTR_TABLE_NAME)->where("goods_id = '%d'", $goods_id)->select(); // 查询商品属性表
        $spec_goods_price = M('shop_spec_goods_price')->where("goods_id = '%d'",
            $goods_id)->getField("key,price,store_count"); // 规格 对应 价格 库存表
        $filter_spec = GoodsService::get_spec($goods_id);
        $data = [
            'goods_info' => $goods,
            'goods_images_list' => $goods_images_list, //商品图册
            'goods_attribute' => $goods_attribute, //商品所属的属性
            'goods_attr_list' => $goods_attr_list, //商品属性的值
            'spec_goods_price' => $spec_goods_price, //各个规格商品的价格
            'filter_spec' => $filter_spec //商品所属规格信息
        ];

        $this->success($data, '', true);
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

        $goods_service = new GoodsService();
        $goods_res = $goods_service->get_goods_list($where, $catid, $order, $onsale, $page, $limit);
        $this->success($goods_res, '', true);
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

