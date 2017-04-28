<?php

// +----------------------------------------------------------------------
// | 商品类型管理
// +----------------------------------------------------------------------

namespace Shop\Controller;

use Common\Controller\AdminBase;
use Shop\Model\GoodsModel;
use Shop\Service\BrandService;
use Shop\Service\CartService;
use Shop\Service\CategoryService;
use Shop\Service\GoodsService;

class GoodsController extends AdminBase {
    /**
     * 商品列表
     */
    public function index() {
        $brandList = BrandService::getSortBrands()['data'];
        $categoryList = GoodsService::getSortCategory()['data'];
        $this->assign('categoryList', $categoryList);
        $this->assign('brandList', $brandList);
        $this->display();
    }

    /**
     *  获取商品列表接口
     */
    function goodsList() {
        $where = [];
        I('intro') ? $where['intro'] = I('intro') : '';
        I('brand_id') ? $where['brand_id'] = I('brand_id') : '';
        (I('is_on_sale') !== '') ? $where['is_on_sale'] = I('is_on_sale') : '';
        $cat_id = I('cat_id');
        // 关键词搜索
        $key_word = I('key_word') ? trim(I('key_word')) : '';
        if ($key_word) {
            $where['goods_name'] = ['like', '%' . $key_word . '%'];
        }
        // 分类筛选
        if ($cat_id > 0) {
            $grandson_ids = getCatGrandson($cat_id);
            $where['cat_id'] = ['in', $grandson_ids];
        }

        $model = M(GoodsService::GOODS_TABLE_NAME);
        $page = I('page', 1);
        $limit = I('limit', 20);
        $total = $model->where($where)->count();
        $page_count = ceil($total / $limit);
        $order_str = I('orderby');
        $goodsList = $model->where($where)->order($order_str)->page($page, $limit)->order($order_str)->select();

        //分类
        $catList = D(CategoryService::TABLE_NAME)->select();
        $catList = convert_arr_key($catList, 'id');

        foreach ($goodsList as $key => $item) {
            $goodsList[$key]['cat_name'] = $catList[$item['cat_id']]['name'];
        }

        $result = [
            'lists' => $goodsList ? $goodsList : [],
            'page' => $page,
            'total' => $total,
            'page_count' => $page_count
        ];

        $this->success($result);
    }

    /**
     * 添加修改商品
     */
    public function addEditGoods() {
        $GoodsService = new GoodsService();
        $Goods = new GoodsModel();
        $type = $_POST['goods_id'] > 0 ? 2 : 1; // 标识自动验证时的 场景 1 表示插入 2 表示更新
        //ajax提交验证
        if (IS_AJAX && IS_POST) {
            // 根据表单提交的POST数据创建数据对象
            if (!$Goods->create(null, $type)) {
                //  编辑
                $error = $Goods->getError();
                $error_msg = array_values($error);
                $return_arr = array(
                    'status' => -1,
                    'msg' => $error_msg[0],
                    'data' => $error,
                );
                $this->ajaxReturn($return_arr);
            } else {
                //  form表单提交
                $Goods->on_time = time(); // 上架时间
                $_POST['cat_id_2'] && ($Goods->cat_id = $_POST['cat_id_2']);
                $_POST['cat_id_3'] && ($Goods->cat_id = $_POST['cat_id_3']);

                $_POST['extend_cat_id_2'] && ($Goods->extend_cat_id = $_POST['extend_cat_id_2']);
                $_POST['extend_cat_id_3'] && ($Goods->extend_cat_id = $_POST['extend_cat_id_3']);

                if ($type == 2) {
                    $goods_id = $_POST['goods_id'];
                    $Goods->save(); // 写入数据到数据库
                    // 修改商品后购物车的商品价格也修改一下
                    M(CartService::TABLE_NAME)->where("goods_id = $goods_id and spec_key = ''")->save(array(
                        'market_price' => $_POST['market_price'], //市场价
                        'goods_price' => $_POST['shop_price'], // 本店价
                        'member_goods_price' => $_POST['shop_price'], // 会员折扣价
                    ));
//                    echo 'ok';exit;
                    $Goods->afterSave($goods_id);
                } else {
                    $goods_id = $insert_id = $Goods->add(); // 写入数据到数据库
                    $Goods->afterSave($goods_id);
                }

                //暂时关闭属性的操作
                $GoodsService->saveGoodsAttr($goods_id, $_POST['goods_type']); // 处理商品 属性

                $return_arr = array(
                    'status' => 1,
                    'msg' => '操作成功',
                    'data' => array('url' => U('Shop/Goods/index')),
                );
                $this->ajaxReturn($return_arr);
            }
        }

        $goodsInfo = M(GoodsService::GOODS_TABLE_NAME)->where('goods_id=' . I('GET.id', 0))->find();
        $level_cat = GoodsService::find_parent_cat($goodsInfo['cat_id'])['data']; // 获取分类默认选中的下拉框
        $level_cat2 = GoodsService::find_parent_cat($goodsInfo['extend_cat_id'])['data']; // 获取分类默认选中的下拉框
        $cat_list = M(CategoryService::TABLE_NAME)->where("parent_id = 0")->select(); // 已经改成联动菜单
        $brandList = BrandService::getSortBrands()['data'];
        $goodsType = M(GoodsService::GOODS_TYPE_TABLE_NAME)->select();
        $suppliersList = M(GoodsService::SUPPLIERS_TABLE_NAME)->select();
        $this->assign('suppliersList', $suppliersList);
        $this->assign('level_cat', $level_cat);
        $this->assign('level_cat2', $level_cat2);
        $this->assign('cat_list', $cat_list);
        $this->assign('brandList', $brandList);
        $this->assign('goodsType', $goodsType);
        $this->assign('goodsInfo', $goodsInfo);  // 商品详情
        $goodsImages = M("GoodsImages")->where('goods_id =' . I('GET.id', 0))->select();
        $this->assign('goodsImages', $goodsImages);  // 商品相册
        $this->initEditor(); // 编辑器
        $this->display('add_edit_goods');
    }

    /**
     * 删除商品
     */
    public function delGoods() {
        $goods_id = $_GET['id'];
        $error = '';

        // 判断此商品是否有订单
        $c1 = M('OrderGoods')->where("goods_id = $goods_id")->count('1');
        $c1 && $error .= '此商品有订单,不得删除! <br/>';

        // 商品退货记录
        $c1 = M('shop_return_goods')->where("goods_id = $goods_id")->count('1');
        $c1 && $error .= '此商品有退货记录,不得删除! <br/>';

        if ($error) {
            $return_arr = array(
                'status' => -1,
                'msg' => $error,
                'data' => '',
            );   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);
            $this->ajaxReturn($return_arr);
        }

        // 删除此商品        
        M(GoodsService::GOODS_TABLE_NAME)->where('goods_id =' . $goods_id)->delete();  //商品表
        M(CartService::TABLE_NAME)->where('goods_id =' . $goods_id)->delete();  // 购物车
        // M("comment")->where('goods_id ='.$goods_id)->delete();  //商品评论
        // M("goods_consult")->where('goods_id ='.$goods_id)->delete();  //商品咨询
        M("goods_images")->where('goods_id =' . $goods_id)->delete();  //商品相册
        M("spec_goods_price")->where('goods_id =' . $goods_id)->delete();  //商品规格
        M("spec_image")->where('goods_id =' . $goods_id)->delete();  //商品规格图片
        // M("goods_attr")->where('goods_id ='.$goods_id)->delete();  //商品属性     
        // M("goods_collect")->where('goods_id ='.$goods_id)->delete();  //商品收藏          

        $return_arr = array(
            'status' => 1,
            'msg' => '操作成功',
            'data' => '',
        );   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);
        $this->ajaxReturn($return_arr);
    }


    /**
     * 动态获取商品规格选择框 根据不同的数据返回不同的选择框
     */
    public function ajaxGetSpecSelect() {
        $goods_id = $_GET['goods_id'] ? $_GET['goods_id'] : 0;
        //$_GET['spec_type'] =  13;
        $specList = M('Spec')->where("type_id = " . $_GET['spec_type'])->order('`order` desc')->select();
        foreach ($specList as $k => $v) {
            $specList[$k]['spec_item'] = M('SpecItem')->where("spec_id = " . $v['id'])->order('id')->getField('id,item');
        } // 获取规格项

        $items_id = M('SpecGoodsPrice')->where('goods_id = ' . $goods_id)->getField("GROUP_CONCAT(`key` SEPARATOR '_') AS items_id");
        $items_ids = explode('_', $items_id);

        // 获取商品规格图片                
        if ($goods_id) {
            $specImageList = M('SpecImage')->where("goods_id = $goods_id")->getField('spec_image_id,src');
        }
        $this->assign('specImageList', $specImageList);

        $this->assign('items_ids', $items_ids);
        $this->assign('specList', $specList);
        $this->display('ajax_spec_select');
    }

    /**
     * 动态获取商品规格输入框 根据不同的数据返回不同的输入框
     */
    public function ajaxGetSpecInput() {
        $GoodsService = new GoodsService();
        $goods_id = $_REQUEST['goods_id'] ? $_REQUEST['goods_id'] : 0;
        $str = $GoodsService->getSpecInput($goods_id, $_POST['spec_arr']);
        exit($str);
    }

    /**
     * 删除商品相册图
     */
    public function del_goods_images() {
        $path = I('filename', '');
        M('goods_images')->where("image_url = '$path'")->delete();
    }

    /**
     * 动态获取商品属性输入框 根据不同的数据返回不同的输入框类型
     */
    public function ajaxGetAttrInput() {
        $GoodsService = new GoodsService();
        $str = $GoodsService->getAttrInput($_REQUEST['goods_id'], $_REQUEST['type_id']);
        exit($str);
    }

    /**
     * 初始化编辑器链接
     * 本编辑器参考 地址 http://fex.baidu.com/ueditor/
     */
    private function initEditor() {
        $this->assign("URL_upload", U('Shop/Ueditor/imageUp', array('savepath' => 'goods'))); // 图片上传目录
        $this->assign("URL_imageUp", U('Shop/Ueditor/imageUp', array('savepath' => 'article'))); //  不知道啥图片
        $this->assign("URL_fileUp", U('Shop/Ueditor/fileUp', array('savepath' => 'article'))); // 文件上传s
        $this->assign("URL_scrawlUp", U('Shop/Ueditor/scrawlUp', array('savepath' => 'article')));  //  图片流
        $this->assign("URL_getRemoteImage", U('Shop/Ueditor/getRemoteImage', array('savepath' => 'article'))); // 远程图片管理
        $this->assign("URL_imageManager", U('Shop/Ueditor/imageManager', array('savepath' => 'article'))); // 图片管理
        $this->assign("URL_getMovie", U('Shop/Ueditor/getMovie', array('savepath' => 'article'))); // 视频上传
        $this->assign("URL_Home", "");
    }

    /**
     * 更改指定表的指定字段
     */
    public function updateField() {
        $primary = array(
            'goods' => 'goods_id',
            'goods_category' => 'id',
            'brand' => 'id',
            'goods_attribute' => 'attr_id',
            'ad' => 'ad_id',
        );
        $model = D($_POST['table']);
        $model->$primary[$_POST['table']] = $_POST['id'];
        $model->$_POST['field'] = $_POST['value'];
        $model->save();
        $return_arr = array(
            'status' => 1,
            'msg' => '操作成功',
            'data' => array('url' => U('Goods/index')),
        );
        $this->success($return_arr);
    }
}