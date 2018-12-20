<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 16:41
 */

namespace App\Models\MiniProgram\Party;

use App\Models\MiniProgram\User\User;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'message';

    public $timestamps = false;
    protected $fillable =
        [
            'id',
            'content',
            'party_id',
            'user_id',
            'created_at',
            'updated_at'
        ];

    const HOST = '发起者';
    const JOIN = '参与者';
    const PASSERS = '围观者';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 用户
     */
    public function user()
    {
        return $this->hasOne(User::Class, 'id', 'user_id');
    }
}