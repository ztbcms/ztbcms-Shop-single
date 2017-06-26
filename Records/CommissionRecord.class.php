<?php
/**
 * 提成交易记录record
 */
namespace Shop\Records;

use Record\Libs\Record;

class CommissionRecord extends Record {
    public $table_name = 'RecordCommission';

    public function __construct($to, $target_type, $target) {
        $this->setTo($to);
        $this->setTargetType($target_type);
        $this->setTarget($target);
    }
}