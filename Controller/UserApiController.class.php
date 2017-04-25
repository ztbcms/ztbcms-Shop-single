<?php
namespace Shop\Controller;

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

        $user_service = new UserService();
        $res = $user_service->login($username, $password);
        if ($res) {
            unset($res['password']);
            unset($res['encrypt']);
            $this->success($res, '', true);
        } else {
            $this->error($user_service->get_err_msg(), '', true);
        }
    }

    public function register() {
        //验证码检验
        $username = I('post.username', '');
        $password = I('post.password', '');
        $password2 = I('post.password2', '');
        $share_id = I('post.share_id', null);
        $user_service = new UserService();
        $res = $user_service->register($username, $password, $password2, $share_id);
        if ($res) {
            session('user', $res);
            unset($res['password']);
            unset($res['encrypt']);
            $this->success($res, '', true);
        } else {
            $this->error($user_service->get_err_msg(), '', true);
        }
    }

    /**
     * 用户地址列表
     */
    public function address_list() {
        $address_lists = M(UserService::ADDRESS_TABLE_NAME)->where(array('userid' => $this->userid))->order('is_default desc')->select();
        $list = [];
        foreach ($address_lists as $key => $value) {
            $value['province_name'] = getRegionName($value['province'], 1);
            $value['city_name'] = getRegionName($value['city'], 2);
            $value['district_name'] = getRegionName($value['district'], 3);
            $list[] = $value;
        }
        $this->success($list, '', true);
    }

    /**
     * 添加地址
     */
    public function add_address() {
        if (IS_POST) {
            $user_service = new UserService();
            $data = $user_service->add_eidt_address($this->userid, 0, I('post.'));
            if ($data) {
                $this->success($data, '', true);
            } else {
                $this->error($user_service->get_err_msg(), '', true);
            }
        } else {
            $this->error('请求方法错误', '', true);
        }
    }

    /**
     * 编辑地址
     */
    public function edit_address() {
        if (IS_POST) {
            $address_id = I('post.address_id');
            if (!$address_id) {
                $this->error('参数错误', '', true);
            }
            $post = I('post.');
            unset($post['address_id']);
            $user_service = new UserService();
            $data = $user_service->add_eidt_address($this->userid, $address_id, I('post.'));
            $this->success($data, '', true);
        } else {
            $this->error('请求方法错误', '', true);
        }
    }

    /**
     * 设置默认收货地址
     */
    public function set_default() {
        $id = I('post.id');
        M(UserService::ADDRESS_TABLE_NAME)->where(array('userid' => $this->userid))->save(array('is_default' => 0));
        $row = M(UserService::ADDRESS_TABLE_NAME)->where(array(
            'userid' => $this->userid,
            'address_id' => $id
        ))->save(array('is_default' => 1));
        if (!$row) {
            $this->error('操作失败', '', true);
        } else {
            $this->success('操作成功', '', true);
        }
    }

    /**
     * 地址删除
     */
    public function del_address() {
        $id = I('post.id');
        $address = M(UserService::ADDRESS_TABLE_NAME)->where("address_id = $id")->find();
        $row = M(UserService::ADDRESS_TABLE_NAME)->where(array('userid' => $this->userid, 'address_id' => $id))->delete();
        // 如果删除的是默认收货地址 则要把第一个地址设置为默认收货地址
        if ($address['is_default'] == 1) {
            $address2 = M(UserService::ADDRESS_TABLE_NAME)->where("userid = {$this->userid}")->find();
            $address2 && M(UserService::ADDRESS_TABLE_NAME)->where("address_id = {$address2['address_id']}")->save(array('is_default' => 1));
        }
        if (!$row) {
            $this->error('操作失败', '', true);
        } else {
            $this->success('操作成功', '', true);
        }
    }
}