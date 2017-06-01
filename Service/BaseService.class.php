<?php
namespace Shop\Service;

class BaseService {

    /**
     * 存放错误信息
     *
     * @var string
     */
    public $err_msg = '';

    /**
     * 设置错误信息
     *
     * @param $err_msg
     */
    public function set_err_msg($err_msg) {
        $this->err_msg = $err_msg;
    }

    /**
     * 返回最近的错误信息
     *
     * @return string
     */
    public function get_err_msg() {
        return $this->err_msg;
    }

    /**
     * 创建统一的Service返回结果
     *
     * @param boolean $status
     * @param array   $data
     * @param string  $msg
     * @return array
     */
    protected static function createReturn($status, $data = [], $msg = '') {
        return [
            'status' => $status,
            'data' => $data,
            'msg' => $msg
        ];
    }

    /**
     * 获取数组中的某一列
     * @param array $arr 数组
     * @param string $key_name  列名
     * @return array  返回那一列的数组
     */
    function get_arr_column($arr, $key_name) {
        $arr2 = array();
        foreach ($arr as $key => $val) {
            $arr2[] = $val[$key_name];
        }
        return $arr2;
    }
}