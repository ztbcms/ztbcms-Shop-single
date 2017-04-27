<?php

// +----------------------------------------------------------------------
// | 商品分类管理
// +----------------------------------------------------------------------

namespace Shop\Controller;

use Common\Controller\AdminBase;
use Shop\Logic\GoodsLogic;
use Shop\Service\CategoryService;

class CategoryController extends AdminBase {
    /**
     * 商品分类展示
     */
    public function index() {
        if (IS_AJAX) {
            $CategoryService = new CategoryService();
            $cat_list = $CategoryService->goods_cat_list();

            // 解决ajax的自动排序
            $arr = [];
            $num = 0;
            foreach ($cat_list as $key => $val) {
                $arr[$num] = $val;
                $num++;
            }

            $this->ajaxReturn(['cat_list' => $arr]);
        }
        $this->display();
    }

    public function getCategoryDetail() {
        $id = I('id', 0);
        if (IS_AJAX) {
            $cat_list = M('goods_category')->select(); // 已经改成联动菜单
            $cat_list = convert_arr_key($cat_list, 'id');
            if ($id == 0) {
                $goods_category_info = [
                    'name' => '',
                    'mobile_name' => '',
                    'parent_id' => 0,
                    'is_show' => 1,
                    'cat_group' => 0,
                    'image' => '',
                    'sort_order' => 50,
                    'commission_rate' => 50,
                ];
                $pid = 0;
            } else {
                $goods_category_info = D('GoodsCategory')->where(['id' => $id])->find();
                if ($goods_category_info['level'] == 3) {
                    $pid = $cat_list[$goods_category_info['parent_id']]['parent_id'];
                } else {
                    $pid = 0;
                }
            }

            $this->ajaxReturn(['cat_list' => $cat_list, 'goods_category_info' => $goods_category_info, 'pid' => $pid]);
        }
        $this->assign('id', $id);
        $this->display('_category');

    }

    /**
     * 添加修改商品分类
     * 手动拷贝分类正则 ([\u4e00-\u9fa5/\w]+)  ('393','$1'),
     * select * from tp_goods_category where id = 393
     * select * from tp_goods_category where parent_id = 393
     * update tp_goods_category  set parent_id_path = concat_ws('_','0_76_393',id),`level` = 3 where parent_id = 393
     * insert into `tp_goods_category` (`parent_id`,`name`) values
     * ('393','时尚饰品'),
     */
    public function addEditCategory() {
        $data = I('detail');
        $id = I('id', 0);

        if ($data['commission_rate'] > 100) {
            $this->ajaxReturn(['status' => false, 'msg' => '分佣比例不得超过100%']);
        }

        $pid = I('pid', 0);
        $path = '0_';
        if ($pid != 0) {
            $path .= $pid . "_";
        }

        if ($data['parent_id'] == 0) {
            $path = '0_';
        } else {
            $path .= $data['parent_id'] . '_';
        }

        $data['level'] = substr_count($path, '_');

        if ($id == 0) {
            unset($data['id']);

            $insert_id = M('goodsCategory')->add($data); // 写入数据到数据库

            if (!$insert_id) {
                $this->ajaxReturn(['status' => false, 'msg' => '操作失败']);
            }
            $path .= $insert_id;
            M('goodsCategory')->where(['id' => $insert_id])->save(['parent_id_path' => $path]);
            $this->ajaxReturn(['status' => 1, 'msg' => '操作成功']);
        } else {
            unset($data['id']);

            if ($data['parent_id'] == $id) {
                $this->ajaxReturn(['status' => false, 'msg' => '上级分类不能为自己']);
            }

            $path .= $id;
            $data['parent_id_path'] = $path;

            $res = M('goodsCategory')->where(['id' => $id])->save($data);
            if (!$res) {
                $this->ajaxReturn(['status' => false, 'msg' => '操作失败或没有修改']);
            }
            $this->ajaxReturn(['status' => 1, 'msg' => '操作成功']);

        }

    }

    /**
     * 删除分类
     */
    public function delGoodsCategory() {
        $id = I('post.id');
        // 判断子分类
        $GoodsCategory = M("GoodsCategory");
        $count = $GoodsCategory->where("parent_id = '%d'", $id)->count("id");
        $count > 0 && $this->error('该分类下还有分类不得删除!');
        // 判断是否存在商品
        $goods_count = M('Goods')->where("cat_id = '%d'", $id)->count('1');
        $goods_count > 0 && $this->error('该分类下有商品不得删除!');
        // 删除分类
        $GoodsCategory->where("id = '%d'", $id)->delete();
        $this->success("操作成功!!!");
    }

}
