<?php
namespace Shop\Service;

use Record\Records\TradeRecord;
use Record\Service\TradeRecordService;

class UserService extends BaseService {

    //定义用户地址表名
    const ADDRESS_TABLE_NAME = 'ShopUserAddress';

    public function login($username, $password) {
        if (!$username || !$password) {
            $this->set_err_msg('请填写账号或密码');

            return false;
        }
        //mobile_ 拼凑作为cms的用户名。
        $userid = service('Passport')->loginLocal('mobile_' . $username, $password, 7 * 86400);
        if (!$userid) {
            $this->set_err_msg('账号/密码错误');

            return false;
        }
        $user = M('Member')->where("userid='%d'", $userid)->find();

        return $user;
    }

    /**
     * 用户注册
     *
     * @param $username 用户名，默认是使用手机登录
     * @param $password
     * @param $password2
     * @param $share_id 推荐人id
     * @return array
     */

    public function register($username, $password, $password2, $share_id = null) {

        $is_validated = 0;
        //检查是手机
        if (self::checkMobile($username)) {
            $is_validated = 1;
            $map['mobile_validated'] = 1;
            $map['nickname'] = $map['mobile'] = $username; //手机注册
            $member_username = "mobile_" . $username;
        }

        if ($is_validated != 1) {
            $this->set_err_msg('请用手机注册');

            return false;
        }

        if (!$username || !$password) {
            $this->set_err_msg('请输入用户名或密码');

            return false;
        }

        //验证两次密码是否匹配
        if ($password2 != $password) {
            $this->set_err_msg('两次输入密码不一致');

            return false;
        }

        //验证是否存在用户名
        if (self::getUserInfo($member_username)) {
            $this->set_err_msg('账号已存在');

            return false;
        }

        $map['token'] = md5(time() . mt_rand(1, 99999));
        $member_user_id = service("Passport")->userRegister($member_username, $password, $map['mobile'] . "@139.com");
        if (!$member_user_id) {
            $this->set_err_msg('注册失败1');

            return false;
        } else {
            $map['userid'] = $member_user_id;
            $user_id = M('ShopUsers')->add($map);
            if (!$user_id) {
                M('Member')->delete($member_user_id);
                $this->set_err_msg('注册失败2');

                return false;
            }
        }
        //如果有推荐者id传入
        if ($share_id) {
            self::share($member_user_id, $share_id);
        }

        $user = M('Member')->where(['userid' => $member_user_id])->find();

        return $user;
    }

    /**
     * 添加编辑地址信息
     *
     * @param int   $user_id    用户ID
     * @param int   $address_id 地址ID
     * @param array $data       传入参数
     * @return bool|int|mixed
     */
    public function add_eidt_address($user_id, $address_id = 0, $data) {
        $post = $data;
        if ($address_id == 0) {
            $c = M(self::ADDRESS_TABLE_NAME)->where("userid = $user_id")->count();
            if ($c >= 20) {
                $this->set_err_msg('最多只能添加20个收货地址');

                return false;
            }
        }

        //检查手机格式
        if ($post['consignee'] == '') {
            $this->set_err_msg('收货人不能为空');

            return false;
        }
        if (!$post['province'] || !$post['city'] || !$post['district']) {
            $this->set_err_msg('所在地区不能为空');

            return false;
        }
        if (!$post['address']) {
            $this->set_err_msg('地址不能为空');

            return false;
        }
        if (!self::checkMobile($post['mobile'])) {
            $this->set_err_msg('手机号码格式有误' . $post['mobile']);

            return false;
        }

        //编辑模式
        if ($address_id > 0) {
            $address = M(self::ADDRESS_TABLE_NAME)->where(array('address_id' => $address_id, 'userid' => $user_id))->find();
            if ($post['is_default'] == 1 && $address['is_default'] != 1) {
                M(self::ADDRESS_TABLE_NAME)->where(array('userid' => $user_id))->save(array('is_default' => 0));
            }
            $row = M(self::ADDRESS_TABLE_NAME)->where(array('address_id' => $address_id, 'user_id' => $user_id))->save($post);
            if (!$row) {
                return true;
            }

            return true;
        }
        //添加模式
        $post['userid'] = $user_id;

        // 如果目前只有一个收货地址则改为默认收货地址
        $c = M(self::ADDRESS_TABLE_NAME)->where("userid = {$post['userid']}")->count();
        if ($c == 0) {
            $post['is_default'] = 1;
        }

        $address_id = M(self::ADDRESS_TABLE_NAME)->add($post);
        //如果设为默认地址
        $insert_id = M(self::ADDRESS_TABLE_NAME)->getLastInsID();
        $map['userid'] = $user_id;
        $map['address_id'] = array('neq', $insert_id);

        if ($post['is_default'] == 1) {
            M(self::ADDRESS_TABLE_NAME)->where($map)->save(array('is_default' => 0));
        }
        if (!$address_id) {
            $this->set_err_msg('添加失败');

            return false;
        }

        return $address_id;
    }

    /**
     * 通过用户名获取用户信息
     *
     * @param $username
     * @return bool|mixed
     */
    static function getUserInfo($username) {
        $member = M('Member')->where(['username' => $username])->find();
        if ($member) {
            return $member;
        } else {
            return false;
        }
    }

    /**
     * 检查是否是手机
     *
     * @param $mobile
     * @return bool
     */
    static function checkMobile($mobile) {
        if (preg_match('/1[34578]\d{9}$/', $mobile)) {
            return true;
        }

        return false;
    }

    /**
     * 上下级关系处理
     *
     * @param $userid   注册用户
     * @param $share_id 上级用户
     * @return bool
     */
    static function share($userid, $share_id) {
        $share = M('ShopUsers')->where(['userid' => $share_id])->find();
        if ($share) {
            //如果找到该上级，将user的直接上级改成 $share_id
            $update = [
                'direct_leader' => $share['userid']
            ];
            //TODO 根据自己的页面还可以添加分级推荐关系  以下是举例
            if ($share['level'] == 1) {
                //分享人是一级
                $update['first_leader'] = $share['userid'];
            } elseif ($share['level'] == 2) {
                //分享人是二级
                $update['first_leader'] = $share['first_leader'];
                $update['second_leader'] = $share['userid'];
            } elseif ($share['level'] == 3) {
                //分享人是三级
                $update['first_leader'] = $share['first_leader'];
                $update['second_leader'] = $share['second_leader'];
                $update['third_leader'] = $share['userid'];
            } else {
                //无等级
                $update['first_leader'] = $share['first_leader'];
                $update['second_leader'] = $share['second_leader'];
                $update['third_leader'] = $share['third_leader'];
            }

            return M('ShopUsers')->where(['userid' => $userid])->save($update);
        } else {
            return false;
        }
    }

    /**
     * 获取用户的余额
     *
     * @param $userid
     * @return int
     */
    public function getBalance($userid) {
        $trade_recorde = new TradeRecord();
        $trade_recorde->setTo($userid);
        $trade_recorde->setToType('member');
        $res = TradeRecordService::getBalance($trade_recorde);

        return $res['status'] ? $res['data'] : 0;
    }
}