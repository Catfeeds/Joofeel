<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/18
 * Time: 14:29
 */

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Inventory;
use App\Utils\ResponseUtil;

class InventoryController extends Controller
{
    public function get()
    {

        $data = Inventory::all();
        return ResponseUtil::toJson($data);
    }
}