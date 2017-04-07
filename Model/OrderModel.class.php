<?php
namespace Shop\Model;

use Common\Model\Model;

class OrderModel extends Model {
    /**
     * var 已支付
     */
    const PAY_STATUS_YES = 1;
    /**
     * var 未支付
     */
    const PAY_STATUS_NO = 0;

    /**
     * var 待确认
     */
    const STATUS_WAIT_CONFIRM = 0;
    /**
     * var 已确认
     */
    const STATUS_CONFIRM = 1;
    /**
     * var 已收货
     */
    const STATUS_SHIPPING = 2;
    /**
     * var 已取消
     */
    const STATUS_CANCEL = 3;
    /**
     * var 已完成
     */
    const STATUS_FINISH = 4;
    /**
     * var 已无效
     */
    const STATUS_INVALID = 5;
}