<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/14
 * Time: 13:52
 */

namespace App\Http\Controllers\Api\v1\Config;


use App\Http\Controllers\Controller;
use App\Models\FormId;
use App\Utils\ResponseUtil;

class MessageController extends Controller
{
    public function send()
    {
        $formId = FormId::where('isUse',FormId::NOT_USE)
                        ->leftJoin('user as u','u.id','=','form_id.user_id')
                        ->select('form_id.form_id','user.openid')
                        ->get();
        return ResponseUtil::toJson($formId);
    }
}