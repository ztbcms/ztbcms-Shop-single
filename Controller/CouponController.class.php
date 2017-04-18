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
     * 添加优惠券
     */
    public function add_coupon()
    {
        if(IS_POST){
            $post = I('post.');
            $res = M('ShopCoupon')->add($post);
            if($res){
                $this->ajaxReturn(['status'=>1,'msg'=>'添加成功']);
            }else{
                $this->ajaxReturn(['status'=>0,'msg'=>'操作失败']);
            }
        }else{
            $this->display();
        }
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

    /**
     * 删除商城优惠
     */
    public function delete_coupon()
    {
        $res = M('ShopCoupon')->where('id='.I('post.id'))->delete();
        if($res){
            $this->ajaxReturn(['status'=>1, 'msg'=>'删除成功']);
        }else{
            $this->ajaxReturn(['status'=>0, 'msg'=>'操作失败']);
        }
    }

    /**
     * 用户优惠券列表
     */
    public function user_coupon()
    {
        if(IS_POST){
            $where = [];
            $model = M('ShopUsercoupon');
            $page = I('post.page');
            $limit = I('post.limit');
            $total = $model->where($where)->count();
            $page_count = ceil($total / $limit);
            $lists = $model->where($where)->order('id DESC')->page($page, $limit)->select();
            $result = [
                'lists' => $lists ? $lists : [],
                'page' => $page,
                'total' => $total,
                'page_count' => $page_count
            ];
            $this->success($result,'',true);
        }else{
            $this->display();
        }
    }

    /**
     * 选择优惠券
     */
    public function getCouponLists()
    {
        $res = M('ShopCoupon')->where('status = 1')->select();//查出所有正常的优惠券
        if($res){
            $this->ajaxReturn(['status'=>1,'info'=>$res,'msg'=>'ok']);
        }
    }

    /**
     * 选择会员
     */
    public function getUserLists()
    {
        $res = M('ShopUsers')->where('is_lock = 0')->select();//查出所有未冻结的会员
        if($res){
            $this->ajaxReturn(['status'=>1,'info'=>$res,'msg'=>'ok']);
        }
    }

    /**
     * 添加用户优惠券
     */
    public function add_user_coupon()
    {
        if(IS_POST){
            $coupon_info = M('ShopCoupon')->where('id='.I('post.coupon_id'))->find();
            $coupon_info['coupon_id'] = $coupon_info['id'];
        }else{
            $this->display();
        }
    }
}