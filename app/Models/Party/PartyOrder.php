<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 16:38
 */

namespace App\Models\Party;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class PartyOrder extends Model
{
    const NOT_DELETE = 0;

    const DELETE = 1;

    protected $table = 'party_order';

    public $timestamps = false;

    protected $fillable =
        [
      //      'id',
            'user_id',
            'party_id',
     //       'isDeleteUser',
    //        'created_at',
   //         'updated_at'
        ];

    public function party(){
        return $this->belongsTo(Party::class,'party_id','id');
    }

    public function user()
    {
        return $this->hasOne(User::Class, 'id', 'user_id');
    }

    /**
     * @param $uid
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * 获取用户参加过的派对
     */
    static function getUserJoinParty($uid){
        $data = self::with(['party'=>function($query){
            $query->where('isDeleteAdmin',self::NOT_DELETE)
                ->withCount('participants')
                ->withCount('message');
        }])
            ->where('isDeleteUser', self::NOT_DELETE)
            ->where('user_id', $uid)
            ->orderBy('created_at' ,'desc')
            ->get();
        return $data;
    }
}