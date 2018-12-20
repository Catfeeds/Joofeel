<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/20
 * Time: 14:11
 */

namespace App\Services\Enter;

use App\Models\Enter\Merchants;

class MerchantsService
{
    public function get($limit)
    {
        $data = Merchants::withCount('push')
                         ->withCount('ticket')
                         ->orderByDesc('created_at')

                         ->paginate($limit);
        return $data;
    }
}