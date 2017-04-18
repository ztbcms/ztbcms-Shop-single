<?php
namespace Shop\Controller;

use Common\Controller\AdminBase;
use Shop\Util\AjaxPage;
use Shop\Util\Page;
use Shop\Logic\ShopUsersLogic;

class UserController extends AdminBase {
    public function index() {
        $this->display();
    }

    /**
     * 会员列表
     */
    public function ajaxindex() {
        $setWhere = I('where');
        $phone = $setWhere['phone'];
        $where['modelid'] = ['eq', 0];
        $where['username'] = ['like', '%' . $phone . '%'];

        $page = I('page', 1);
        $limit_start = 12 * ($page - 1);
        $limit_end = $page * 12 - 1;
        $limit = $limit_start . ',' . $limit_end;
        $page_count = ceil(M('Member')->where($where)->count() / 12);

        $order = I('order', 'userid');

        $data = M('Member')->where($where)->order($order)->limit($limit)->select();

        $user_id_arr = get_arr_column($data, 'userid');
        if (!empty($user_id_arr)) {
            $first_leader = M('ShopUsers')->query("select first_leader,count(1) as count  from __PREFIX__shop_users where first_leader in(" . implode(',',
                    $user_id_arr) . ")  group by first_leader");
            $first_leader = convert_arr_key($first_leader, 'first_leader');

            $second_leader = M('ShopUsers')->query("select second_leader,count(1) as count  from __PREFIX__shop_users where second_leader in(" . implode(',',
                    $user_id_arr) . ")  group by second_leader");
            $second_leader = convert_arr_key($second_leader, 'second_leader');

            $third_leader = M('ShopUsers')->query("select third_leader,count(1) as count  from __PREFIX__shop_users where third_leader in(" . implode(',',
                    $user_id_arr) . ")  group by third_leader");
            $third_leader = convert_arr_key($third_leader, 'third_leader');
        }

        foreach ($data as $key => &$val) {
            $userid = $val['userid'];
            $res = M('shopUsers')->where('userid = ' . $userid)->find();
            if ($res) {
                $val = array_merge($val, $res);
            }
        }

        $level = M('ShopUserLevel')->getField('level_id,level_name');
        $this->ajaxReturn([
            'page' => $page,
            'page_count' => $page_count,
            'data' => $data,
            'level' => $level,
            'first_leader' => $first_leader,
            'second_leader' => $second_leader,
            'third_leader' => $third_leader
        ]);
    }

    /**
     * 会员详细信息查看
     */
    public function detail() {
        $uid = I('get.id');
        $user = D('ShopUsers')->where(array('userid' => $uid))->find();
        $member = D('Member')->where(array('userid' => $uid))->find();
        if (!$user && !$member) {
            exit($this->error('会员不存在'));
        }
        if (IS_POST) {
            //  会员信息编辑

            $tempData = D('ShopUsers')->where(array('mobile' => $_POST['mobile']))->find();
            if ($tempData) {
                if ($user['mobile'] != $tempData['mobile']) {
                    exit($this->error('此手机号码已经存在'));
                }
            }

            $_POST['username'] = 'mobile_' . $_POST['mobile'];
            $password = I('post.password');
            $password2 = I('post.password2');
            if ($password != '' && $password != $password2) {
                exit($this->error('两次输入密码不同'));
            }
            if ($password == '' || $password2 == '') {
                unset($_POST['password']);
            } else {
                $_POST['password'] = encrypt($_POST['password']);
                service("Passport")->userEdit($member['username'], '', $password, '', 1);
            }
            $row = M('ShopUsers')->where(array('userid' => $uid))->save($_POST);
            if ($row !== false) {
                $row = M('Member')->where(array('userid' => $uid))->save($_POST);
            }
            if ($row !== false) {
                exit($this->success('修改成功'));
            }
            exit($this->error('未作内容修改或修改失败'));
        }

        $user = array_merge($user, $member);

        $this->assign('user', $user);
        $this->display();
    }

    /**
     * 账户资金调节
     */
    public function account_edit() {
        $user_id = I('get.id');
        if (!$user_id > 0) {
            $this->error("参数有误");
        }
        if (IS_POST) {
            //获取操作类型
            $m_op_type = I('post.money_act_type');
            $user_money = I('post.user_money');
            $user_money = $m_op_type ? $user_money : 0 - $user_money;

            $p_op_type = I('post.point_act_type');
            $pay_points = I('post.pay_points');
            $pay_points = $p_op_type ? $pay_points : 0 - $pay_points;

            $f_op_type = I('post.frozen_act_type');
            $frozen_money = I('post.frozen_money');
            $frozen_money = $f_op_type ? $frozen_money : 0 - $frozen_money;

            $desc = I('post.desc');
            if (!$desc) {
                $this->error("请填写操作说明");
            }
            if (accountLog($user_id, $user_money, $pay_points, $desc)) {
                $this->success("操作成功", U("User/account_log", array('id' => $user_id)));
            } else {
                $this->error("操作失败");
            }
            exit;
        }
        $this->assign('user_id', $user_id);
        $this->display();
    }

    /**
     * 搜索用户名
     */
    public function search_user() {
        $search_key = trim(I('search_key'));
        if (strstr($search_key, '@')) {
            $list = M('ShopUsers')->where(" email like '%$search_key%' ")->select();
            foreach ($list as $key => $val) {
                echo "<option value='{$val['userid']}'>{$val['email']}</option>";
            }
        } else {
            $list = M('ShopUsers')->where(" mobile like '%$search_key%' ")->select();
            foreach ($list as $key => $val) {
                echo "<option value='{$val['userid']}'>{$val['mobile']}</option>";
            }
        }
        exit;
    }

    public function add_user() {
        if (IS_POST) {
            $data = I('post.');
            $user_obj = new ShopUsersLogic();
            $res = $user_obj->addUser($data);
            if ($res['status'] == 1) {
                $this->success('添加成功', U('User/index'));
                exit;
            } else {
                $this->error($res['msg'], U('User/index'));
            }
        }
        $this->display();
    }

    /**
     * 用户收货地址查看
     */
    public function address() {
        $uid = I('get.id');
        $lists = D('UserAddress')->where(array('userid' => $uid))->select();
        // 获取省份
        $province = M('AreaProvince')->getField('id,areaname');
        //获取订单城市
        $city = M('AreaCity')->where(array('level' => 2))->getField('id,areaname');
        //获取订单地区
        $district = M('AreaDistrict')->where(array('level' => 3))->getField('id,areaname');
        $this->assign('lists', $lists);
        $this->assign('province', $province);
        $this->assign('city', $city);
        $this->assign('district', $district);
        $this->display();
    }

    public function setDefault_address() {
        if (IS_POST) {
            // 设置默认地址
            $default_id = I('default_id');
            $address_id = I('address_id');
            $res = M('UserAddress')->where(['address_id' => $default_id])->save(['is_default' => 0]);
            if ($res !== false) {
                $res = M('UserAddress')->where(['address_id' => $address_id])->save(['is_default' => 1]);
            }
            if ($res !== false) {
                $this->ajaxReturn(['msg' => '设置成功']);
            }
            $this->ajaxReturn(['msg' => '设置失败']);
        }
    }

    public function add_address() {
        if (IS_POST) {
            $data = I('post.');
            $res = M('userAddress')->add($data);
            if ($res) {
                $this->success('添加成功', U('User/address', ['id' => $data['userid']]));
            } else {
                $this->error('添加失败', U('User/address', ['id' => $data['userid']]));
            }
            exit;
        }
        $this->display();
    }

    public function del_address() {
        if (IS_POST) {
            $id = I('id');
            $res = M('userAddress')->where(['address_id' => $id])->delete();
            if ($res) {
                $this->ajaxReturn(['msg' => '删除成功']);
            }
            $this->ajaxReturn(['msg' => '删除失败']);
        }
    }

    public function update_address() {
        if (IS_POST) {
            $data = I('post.');
            $id = $data['id'];
            $res = M('userAddress')->where(['address_id' => $id])->save($data);
            if ($res) {
                $this->success('修改成功', U('User/address', ['id' => $data['userid']]));
            } else {
                $this->error('修改失败', U('User/address', ['id' => $data['userid']]));
            }
            exit;
        }
        $id = I('id');
        $address = M('userAddress')->where(['address_id' => $id])->find();
        $this->assign('address', $address);
        $this->display();
    }

    /**
     * 删除会员
     */
    public function delete() {
        $uid = I('get.id');
        $row = M('ShopUsers')->where(array('user_id' => $uid))->delete();
        if ($row) {
            $this->success('成功删除会员');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 用户等级管理
     */
    public function level() {
        $this->display();
    }

}