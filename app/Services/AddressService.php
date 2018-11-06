<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/1
 * Time: 12:07
 */

namespace App\Services;

use App\Models\User\DeliveryAddress;

class AddressService extends BaseService
{
    /**
     * @param $id
     * @return mixed
     * 获取地址详情
     */
    public function info($id)
    {
        $data = $this->query($id)
                     ->first();
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     * 新增收货地址
     */
    public function add($data)
    {
        $data['user_id'] = $this->uid;
        $this->checkIsDefault($data['isDefault']);
        $id = DeliveryAddress::insertGetId($data);
        return $id;
    }

    /**
     * @param $data
     * 修改
     */
    public function update($data)
    {
        $this->checkIsDefault($data['isDefault']);
        $this->query($data['id'])
             ->update($data);
    }

    /**
     * @param $isDefault
     * 检查是否为默认地址
     */
    private function checkIsDefault($isDefault)
    {
        if($isDefault == DeliveryAddress::IS_DEFAULT)
        {
            DeliveryAddress::where('isDefault',DeliveryAddress::IS_DEFAULT)
                ->where('user_id',$this->uid)
                ->update([
                    'isDefault' => DeliveryAddress::NOT_DEFAULT
                ]);
        }
    }

    /**
     * @param $id
     * 删除收货地址
     */
    public function delete($id)
    {
        $this->query($id)->delete();
    }

    /**
     * @param $id
     * @return mixed
     * 公用sql语句
     */
    public function query($id)
    {
        $query = DeliveryAddress::where('user_id',$this->uid)
                                ->where('id',$id);
        return $query;
    }
}