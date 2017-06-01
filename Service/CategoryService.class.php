<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * Date: 2017/4/27
 * Time: 16:32
 */

namespace Shop\Service;


class CategoryService extends BaseService {
    /**
     * 获取某个商品分类的 儿子 孙子  重子重孙 的 id
     *
     * @param string $cat_id
     * @return array|string
     */
    public function getCatGrandson($cat_id) {
        $GLOBALS['catGrandson'] = array();
        $GLOBALS['category_id_arr'] = array();
        // 先把自己的id 保存起来
        $GLOBALS['catGrandson'][] = $cat_id;
        // 把整张表找出来
        $GLOBALS['category_id_arr'] = M('ShopGoodsCategory')->getField('id,parent_id');
        // 先把所有儿子找出来
        $son_id_arr = M('ShopGoodsCategory')->where("parent_id = $cat_id")->getField('id', true);
        foreach ($son_id_arr as $k => $v) {
            getCatGrandson2($v);
        }

        return $GLOBALS['catGrandson'];
    }

    //定义商品分类表
    const TABLE_NAME = 'ShopGoodsCategory';

    /**
     * 获得指定分类下的子分类的数组
     *
     * @access  public
     * @param   int     $cat_id   分类的ID
     * @param   int     $selected 当前选中分类的ID
     * @param   boolean $re_type  返回的类型: 值为真时返回下拉列表,否则返回数组
     * @param   int     $level    限定返回的级数。为0时返回所有级数
     * @return  mixed
     */
    public function goods_cat_list($cat_id = 0, $selected = 0, $re_type = true, $level = 0) {
        global $goods_category, $goods_category2;
        $sql = "SELECT * FROM  __PREFIX__shop_goods_category ORDER BY parent_id , sort_order ASC";
        $goods_category = D(self::TABLE_NAME)->query($sql);
        $goods_category = convert_arr_key($goods_category, 'id');

        foreach ($goods_category AS $key => $value) {
            if ($value['level'] == 1) {
                $this->get_cat_tree($value['id']);
            }
        }

        return $goods_category2;
    }

    /**
     * 获取指定id下的 所有分类
     *
     * @global array $goods_category 所有商品分类
     * @param string $id             当前显示的 菜单id
     * @return array 返回数组
     */
    public function get_cat_tree($id) {
        global $goods_category, $goods_category2;
        $goods_category2[$id] = $goods_category[$id];
        foreach ($goods_category AS $key => $value) {
            if ($value['parent_id'] == $id) {
                $this->get_cat_tree($value['id']);
                $goods_category2[$id]['have_son'] = 1; // 还有下级
            }
        }
    }

    static function getGoodsCat($where) {
        $res = M(self::TABLE_NAME)->where($where)->select();

        return self::createReturn(true, $res ? $res : [], '');
    }
}