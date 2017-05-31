<?php
namespace Shop\Controller;

use Libs\System\Service;
use Shop\Service\UserService;

class UserApiController extends BaseController {

    /**
     * 获取登录用户信息
     */
    public function index() {
        $userinfo = service("Passport")->getInfo();
        $userinfo['shop_user'] = $this->shop_user;
        if ($userinfo) {
            unset($userinfo['password']);
            unset($userinfo['encrypt']);
            $this->success($userinfo, '', true);
        } else {
            $this->ajaxReturn(array('status' => -500, 'msg' => '没有登录'));
        }
    }

    /**
     * 获取用户的余额
     */
    public function get_balance() {
        $user_service = new UserService();
        $res = $user_service->getBalance($this->userid);
        $this->success($res);
    }

    /**
     * 用户登录api
     */
    public function login() {
        $username = I('post.username');
        $password = I('post.password');
        $username = trim($username);
        $password = trim($password);


        $res = UserService::login($username, $password);
        if ($res['status']) {
            session('user', $res['data']);
        }
        $this->ajaxReturn($res);
    }

    /**
     * 用户注册api
     */
    public function register() {

        //验证码检验
        $username = I('post.username', '');
        $password = I('post.password', '');
        $password2 = I('post.password2', '');
        $share_id = I('post.share_id', null);

        $res = UserService::register($username, $password, $password2, $share_id);
        if ($res['status']) {
            session('user', $res['data']);
        }
        $this->ajaxReturn($res);
    }

    /**
     * 获取用户地址列表
     */
    public function address_list() {
        if(IS_GET){
            $res = UserService::get_address_list($this->userid);
            $this->ajaxReturn($res);
        }
    }

    /**
     * 添加地址
     */
    public function add_address() {
        if (IS_POST) {
            $res = UserService::add_edit_address($this->userid,0,I('post.'));
            $this->ajaxReturn($res);
        } else {
            $this->ajaxReturn(array('status'=>false, 'data'=>null, 'msg'=>'请求方法错误'));
        }
    }

    /**
     * 编辑地址
     */
    public function edit_address() {
        if (IS_POST) {
            $address_id = I('post.address_id');
            if (!$address_id) {
                $this->ajaxReturn(array('status'=>false, 'data'=>null, 'msg'=>'请求方法错误'));
            }
            $post = I('post.');
            unset($post['address_id']);
            $res = UserService::add_edit_address($this->userid,$address_id,$post);
            $this->ajaxReturn($res);
        } else {
            $this->ajaxReturn(array('status'=>false, 'data'=>null, 'msg'=>'请求方法错误'));
        }
    }

    /**
     * 设置默认收货地址
     */
    public function set_default() {
        if(IS_POST){
            $address_id = I('post.id',0);
            $res = UserService::set_default($this->userid,$address_id);
            $this->ajaxReturn($res);
        }else{
            $this->ajaxReturn(array('status'=>false, 'data'=>null, 'msg'=>'请求方法错误'));
        }
    }

    /**
     * 删除收货地址
     */
    public function del_address() {
        if(IS_POST){
            $address_id = I('post.id',0);
            $res = UserService::del_address($this->userid,$address_id);
            $this->ajaxReturn($res);
        }else{
            $this->ajaxReturn(array('status'=>false, 'data'=>null, 'msg'=>'请求方法错误'));
        }
    }
}