<?php
namespace Shop\Service;

use Record\Records\TradeRecord;
use Record\Service\TradeRecordService;

class UserService extends BaseService {

    //定义用户地址表名
    const ADDRESS_TABLE_NAME = 'ShopUserAddress';


    /**
     * 用户登录
     *
     * @param $username string 用户名，默认是使用手机登录
     * @param $password
     * @return array
     */
    public static function login($username, $password) {
        if (!$username || !$password) {
            return self::createReturn(false, null, '请填写账号或密码');
        }
        //mobile_ 拼凑作为cms的用户名。
        $userid = service('Passport')->loginLocal('mobile_' . $username, $password, 7 * 86400);
        if (!$userid) {
            return self::createReturn(false, null, '账号或密码错误');
        }

        $user = M('Member')->where(['userid' => $userid])->field('password,encrypt',true)->find();

        return self::createReturn(true, $user, '登录成功');

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

    public static function register($username, $password, $password2, $share_id = null) {

        $is_validated = 0;
        //检查是手机
        if (self::checkMobile($username)) {
            $is_validated = 1;
            $map['mobile_validated'] = 1;
            $map['nickname'] = $map['mobile'] = $username; //手机注册
            $member_username = "mobile_" . $username;
        }

        if ($is_validated != 1) {
            return self::createReturn(false, null, '请用手机注册');
        }

        if (!$username || !$password) {

            return self::createReturn(false, null, '请输入用户名或密码');

        }

        //验证两次密码是否匹配
        if ($password2 != $password) {

            return self::createReturn(false, null, '两次输入密码不一致');
        }

        //验证是否存在用户名
        if (self::getUserInfo($member_username)) {

            return self::createReturn(false, null, '账号已存在');

        }

        $map['token'] = md5(time() . mt_rand(1, 99999));
        $member_user_id = service("Passport")->userRegister($member_username, $password, $map['mobile'] . "@139.com");
        if (!$member_user_id) {

            return self::createReturn(false, null, '注册失败1');

        } else {
            $map['userid'] = $member_user_id;
            $user_id = M('ShopUsers')->add($map);
            if (!$user_id) {
                M('Member')->delete($member_user_id);

                return self::createReturn(false, null, '注册失败2');

            }
        }
        //如果有推荐者id传入
        if ($share_id) {
            self::share($member_user_id, $share_id);
        }

        $user = M('Member')->where(['userid' => $member_user_id])->field('password,encrypt',true)->find();

        return self::createReturn(true, $user, '注册成功');
    }


    /**
     * 获取用户地址列表
     * @param $userid int 用户id
     * @return array 地址列表
     */
    public static function get_address_list($userid){

        $address_lists = M(UserService::ADDRESS_TABLE_NAME)->where(array('userid' => $userid))->order('is_default desc')->select();
        $list = [];
        foreach ($address_lists as $key => $value) {
            $value['province_name'] = getRegionName($value['province'], 1);
            $value['city_name'] = getRegionName($value['city'], 2);
            $value['district_name'] = getRegionName($value['district'], 3);
            $list[] = $value;
        }

        return self::createReturn(true, $list, '获取成功');
    }


    /**
     * 添加编辑地址信息
     *
     * @param int   $user_id    用户ID
     * @param int   $address_id 地址ID
     * @param array $data       传入参数
     * @return bool|int|mixed
     */
    public static function add_edit_address($user_id, $address_id = 0, $data) {
        $post = $data;
        if ($address_id == 0) {
            $c = M(self::ADDRESS_TABLE_NAME)->where("userid = $user_id")->count();
            if ($c >= 20) {
                return self::createReturn(false, null, '最多只能添加20个收货地址');
            }
        }

        //检查手机格式
        if ($post['consignee'] == '') {
            return self::createReturn(false, null, '收货人不能为空');
        }
        if (!$post['province'] || !$post['city'] || !$post['district']) {

            return self::createReturn(false, null, '所在地区不能为空');

        }
        if (!$post['address']) {

            return self::createReturn(false, null, '地址不能为空');

        }
        if (!self::checkMobile($post['mobile'])) {
            return self::createReturn(false, null, '手机号码格式有误'. $post['mobile']);
        }

        //编辑模式
        if ($address_id > 0) {
            $address = M(self::ADDRESS_TABLE_NAME)->where(array('address_id' => $address_id, 'userid' => $user_id))->find();
            if ($post['is_default'] == 1 && $address['is_default'] != 1) {
                M(self::ADDRESS_TABLE_NAME)->where(array('userid' => $user_id))->save(array('is_default' => 0));
            }

            $row = M(self::ADDRESS_TABLE_NAME)->where(array('address_id' => $address_id, 'user_id' => $user_id))->save($post);

            $new_address = M(self::ADDRESS_TABLE_NAME)->where(['address_id'=>$address_id])->find();
            if (!$row) {
                return self::createReturn(true, $new_address, '没有修改!');
            }
            return self::createReturn(true, $new_address, '修改成功!');
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

            return self::createReturn(false, null, '添加失败');
        }

        $new_address = M(self::ADDRESS_TABLE_NAME)->where(['address_id'=>$address_id])->find();
        return self::createReturn(true, $new_address, '添加成功');

    }


    /**
     * 删除地址信息
     * @param int   $user_id    用户ID
     * @param int   $address_id 地址ID
     * @return array
     */
    public static function del_address($user_id, $address_id){

        if($address_id == 0){
            return self::createReturn(false, $address_id, '缺少参数!');
        }

        $address = M(UserService::ADDRESS_TABLE_NAME)->where("address_id = $address_id")->find();
        $row = M(UserService::ADDRESS_TABLE_NAME)->where(array('userid' => $user_id, 'address_id' => $address_id))->delete();

        // 如果删除的是默认收货地址 则要把第一个地址设置为默认收货地址
        if ($address['is_default'] == 1) {
            $address2 = M(UserService::ADDRESS_TABLE_NAME)->where(['userid'=>$user_id])->find();
            $address2 && M(UserService::ADDRESS_TABLE_NAME)->where(['address_id' => $address2['address_id']])->save(array('is_default' => 1));
        }
        if (!$row) {
            return self::createReturn(false, $address_id, '删除失败!');
        } else {
            return self::createReturn(true, $address_id, '删除成功!');
        }
    }

    /**
     * 设置默认地址
     * @param int   $user_id    用户ID
     * @param int   $address_id 地址ID
     * @return array
     */
    public static function set_default($user_id, $address_id){
        if($address_id == 0){
            return self::createReturn(false, $address_id, '缺少参数!');
        }

        $row = M(UserService::ADDRESS_TABLE_NAME)->where(array(
            'userid' => $user_id,
            'address_id' => $address_id
        ))->save(array('is_default' => 1));

        $new_address = M(self::ADDRESS_TABLE_NAME)->where(['address_id'=>$address_id])->find();
        if (!$row) {
            return self::createReturn(true, $new_address, '修改失败!');
        } else {
            //将其他地址设置为 不是默认地址
            M(UserService::ADDRESS_TABLE_NAME)->where(array(
                'userid' => $user_id,
                'address_id' => array('neq',$address_id)
            ))->save(array('is_default' => 0));

            return self::createReturn(true, $new_address, '修改成功!');
        }
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