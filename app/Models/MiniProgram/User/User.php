<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 14:28
 */

namespace App\Models\MiniProgram\User;

use App\Models\MiniProgram\FormId;
use App\Models\MiniProgram\Party\Party;
use App\Models\MiniProgram\Party\PartyOrder;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    const IS_NEW = 0;
    const IS_OLD = 1;

    protected $table = 'user';

    public $timestamps = false;

    protected  $fillable =
        [
            'id',
            'openid',
            'nickname',
            'avatar',
            'isNewUser',
            'created_at',
            'updated_at'
        ];

    public function host()
    {
        return $this->hasMany(Party::class,'user_id','id');
    }

    public function join()
    {
        return $this->hasMany(PartyOrder::class,'user_id','id');
    }

    public function formId()
    {
        return $this->hasMany(FormId::class,'user_id','id');
    }

    static function getUser($id)
    {
        $user = self::where('id',$id)->first();
        return $user;
    }
}