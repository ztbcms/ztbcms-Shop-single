<?php
namespace Shop\Service;

class GoodsService extends BaseService {

    /**
     * 获取指定的商品列表
     * @param array $where 查询条件
     * @param int $catid 分类ID
     * @param int $order 排序
     * @param int $onsale 是否上架
     * @param int $page 当前页
     * @param int $limit 每页显示数据
     * @return array
     */
    public function get_goods_list($where, $catid, $order, $onsale, $page = 1, $limit = 20) {
        if ($catid) {
            $where['cat_id'] = ['in', getCatGrandson($catid)];
        }
        $where['is_on_sale'] = $onsale;
        $goods_list = M('Goods')->where($where)->page($page, $limit)->order($order)->select();
        $total_count = M('Goods')->where($where)->count();
        $res = [
            'goods_list' => $goods_list ? $goods_list : [],
            'page' => $page,
            'limit' => $limit,
            'page_count' => ceil($total_count / $limit),
            'total_count' => $total_count,
        ];

        return $res;
    }

    /**
     * 获取相片所属的规格信息
     *
     * @param $goods_id
     * @return array
     */

    static function get_spec($goods_id) {
        //商品规格 价钱 库存表 找出 所有 规格项id
        $keys = M('SpecGoodsPrice')->where("goods_id = $goods_id")->getField("GROUP_CONCAT(`key` SEPARATOR '_') ");
        $filter_spec = array();
        if ($keys) {
            $specImage = M('SpecImage')->where("goods_id = $goods_id and src != '' ")->getField("spec_image_id,src");// 规格对应的 图片表， 例如颜色
            $keys = str_replace('_', ',', $keys);
            $sql = "SELECT a.name,a.order,b.* FROM __PREFIX__spec AS a INNER JOIN __PREFIX__spec_item AS b ON a.id = b.spec_id WHERE b.id IN($keys) ORDER BY b.id";
            $filter_spec2 = M()->query($sql);
            foreach ($filter_spec2 as $key => $val) {
                $filter_spec[$val['name']][] = array(
                    'item_id' => $val['id'],
                    'item' => $val['item'],
                    'src' => $specImage[$val['id']],
                );
            }
        }

        return $filter_spec;
    }
}

