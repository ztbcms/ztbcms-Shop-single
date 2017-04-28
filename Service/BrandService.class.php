<?php
// +-------------------------------------------------------
// | 商品品牌
// +-------------------------------------------------------
// | Author: Nansen Lee [ningshenglee@gmail.com]
// +-------------------------------------------------------

namespace Shop\Service;


class BrandService extends BaseService
{
    //定义商品品牌表名
    const TABLIE_NAME = 'ShopBrand';


    /**
     * 获取排好序的品牌列表
     * @return array
     */
    static function getSortBrands(){
        $brandList =  M(self::TABLIE_NAME)->select();
        $brandIdArr =  M(self::TABLIE_NAME)->where("name in (select `name` from `".C('DB_PREFIX')."shop_brand` group by name having COUNT(id) > 1)")->getField('id,cat_id');
        $goodsCategoryArr = M(CategoryService::TABLE_NAME)->where("level = 1")->getField('id,name');
        $nameList = array();
        foreach($brandList as $k => $v)
        {

            $name = getFirstCharter($v['name']) .'  --   '. $v['name']; // 前面加上拼音首字母

            if(array_key_exists($v[id],$brandIdArr) && $v[cat_id]) // 如果有双重品牌的 则加上分类名称
                $name .= ' ( '. $goodsCategoryArr[$v[cat_id]] . ' ) ';

            $nameList[] = $v['name'] = $name;
            $brandList[$k] = $v;
        }
        array_multisort($nameList,SORT_STRING,SORT_ASC,$brandList);

        return self::createReturn('1',$brandList);
    }
}