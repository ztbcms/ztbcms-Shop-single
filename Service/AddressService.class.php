<?php
namespace Shop\Service;

class AddressService extends BaseService {
    /**
     *
     * @param     $regionId
     * @param int $level
     * @return mixed
     */
    static function getRegionName($regionId, $level = 1) {
        if ($level == 1) {
            $data = M('AreaProvince')->where("id='%d'", $regionId)->find();
        } elseif ($level == 2) {
            $data = M('AreaCity')->where("id='%d'", $regionId)->find();
        } else {
            $data = M('AreaDistrict')->where("id='%d'", $regionId)->find();
        }
        if ($data) {
            return self::createReturn(true, $data['areaname'], '');
        } else {
            return self::createReturn(false, '', '');
        }
    }
}