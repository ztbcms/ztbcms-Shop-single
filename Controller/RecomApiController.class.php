<?php
namespace Shop\Controller;

use Common\Controller\Base;
use Shop\Model\RecomModel;

class RecomApiController extends Base {
    public function getItemByRecomId() {
        $recom_id = I('get.recom_id');
        $where['recom_id'] = $recom_id;
        $where['status'] = RecomModel::STATUS_SHOW;
        $res = M('ShopRecomItem')->where($where)->order('sort desc')->select();
        $this->success($res ? $res : [], '', true);
    }
}