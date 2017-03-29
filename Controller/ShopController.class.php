<?php
namespace Shop\Controller;

use Common\Controller\AdminBase;

class ShopController extends AdminBase {
    public function index() {
        $this->display();
    }

    public function recom() {
        if (IS_AJAX) {
            $res = M('ShopRecom')->order('id desc')->select();
            foreach ($res as $key => $value) {
                $item_list = M('ShopRecomItem')->where(['recom_id' => $value['id']])->order('id desc')->select();
                $res[$key]['item_list'] = $item_list ? $item_list : [];
            }

            return $this->success($res ? $res : [], '', true);
        }
        $this->display();
    }

    /**
     * 添加推荐位
     */
    public function addRecom() {
        $data = I('post.');
        $data['create_time'] = time();
        $res = M('ShopRecom')->add($data);
        if ($res) {
            $this->success('添加成功', '', true);
        } else {
            $this->error('添加失败', '', true);
        }
    }

    /**
     * 添加推荐位
     */
    public function delRecom() {
        $id = I('post.id');
        if (M('ShopRecomItem')->where(['recom_id' => $id])->find()) {
            $this->error('仍有推荐内容，不能删除', '', true);
        }
        $res = M('ShopRecom')->delete($id);
        if ($res) {
            $this->success('删除成功', '', true);
        } else {
            $this->error('删除失败', '', true);
        }
    }

    /**
     * 添加广告位内容
     */
    public function addRecomItem() {
        $data = I('post.');
        $data['create_time'] = time();
        if ($data['id']) {
            $res = M('ShopRecomItem')->save($data);
        } else {
            $res = M('ShopRecomItem')->add($data);
        }
        if ($res) {
            $this->success('添加成功', '', true);
        } else {
            $this->error('添加失败', '', true);
        }
    }

    public function delRecomItem() {
        $id = I('post.id');
        $res = M('ShopRecomItem')->delete($id);
        if ($res) {
            $this->success('删除成功', '', true);
        } else {
            $this->error('删除失败', '', true);
        }
    }
}