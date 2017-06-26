<?php
namespace Shop\Controller;

use Common\Controller\AdminBase;

class ShopController extends AdminBase {
    public function index() {
        if (IS_POST) {
            $post = I('post.');
            foreach ($post as $key => $value) {
                $is_exsit = D('Config')->where("varname='%s'", $key)->find();
                if ($is_exsit) {
                    $data = array('varname' => $key, 'value' => $value);
                    M('Config')->where("id='%d'", $is_exsit['id'])->save($data);
                } else {
                    $data = array('varname' => $key, 'value' => $value);
                    M('Config')->add($data);
                }
            }
            //设置成功后删除缓存
            cache('Config', null);

            $this->success('设置成功');
            return;
        }
        $this->assign('config', cache('Config'));
        $this->display();
    }

    public function recom() {
        if (IS_AJAX) {
            $res = M('ShopRecom')->order('id desc')->select();
            foreach ($res as $key => $value) {
                $item_list = M('ShopRecomItem')->where(['recom_id' => $value['id']])->order('sort desc')->select();
                $res[$key]['item_list'] = $item_list ? $item_list : [];
            }

            $this->success($res ? $res : [], '', true);
            return;
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