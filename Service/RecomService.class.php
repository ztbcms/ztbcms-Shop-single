<?php
namespace Shop\Service;

use Shop\Model\RecomModel;

class RecomService extends BaseService {
    static function getItemByRecomId($recom_id) {
        $where['recom_id'] = $recom_id;
        $where['status'] = RecomModel::STATUS_SHOW;
        $res = M('ShopRecomItem')->where($where)->order('sort desc')->select();

        return self::createReturn(true, $res ? $res : [], '');
    }
}