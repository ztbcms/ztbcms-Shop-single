<?php
// +----------------------------------------------------------------------
// | 优惠券管理
// +----------------------------------------------------------------------
// | Author：Nick [ningshenglee@gmail.com]
// +----------------------------------------------------------------------

namespace Shop\Controller;


use Common\Controller\AdminBase;

class CouponController extends AdminBase
{
    public function index()
    {
        $this->display();
    }

    /**
     * 获取商城优惠券列表
     */
    public function couponList()
    {
        $where = [];
        $model = M('ShopCoupon');
        $page = I('page', 1);
        $limit = I('limit', 20);
        $total = $model->where($where)->count();
        $page_count = ceil($total / $limit);
        $couponList = $model->where($where)->order('id DESC')->page($page, $limit)->select();
        $result = [
            'lists' => $couponList ? $couponList : [],
            'page' => $page,
            'total' => $total,
            'page_count' => $page_count
        ];

        $this->success($result,'',true);
    }

    /**
     * 修改商城优惠券
     */
    public function edit_coupon()
    {
        if(IS_POST){
            $post = I('post.');
            $id = $post['id'];
            unset($post['id']);
            $res = M('ShopCoupon')->where('id='.$id)->save($post);
            if($res){
                $this->ajaxReturn(['status'=>1,'msg'=>'修改成功']);
            }else{
                $this->ajaxReturn(['status'=>0,'msg'=>'没有修改数据']);
            }
        }else{
            $id = I('get.id');
            $coupon = M('ShopCoupon')->where('id='.$id)->find();
            $this->assign('coupon',$coupon);
            $this->display();
        }
    }
}