<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 16:33
 */

namespace App\Models\MiniProgram\Party;

use App\Models\MiniProgram\Order\OrderId;
use App\Models\MiniProgram\User\User;
use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    //派对是否被用户删除
    const NOT_DELETE = 0;
    const DELETE = 1;
    const NOT_HOST =2;

    //派对是否被关闭
    const NOT_CLOSE = 0;
    const CLOSE = 1;
    const DONE = 2;  //提前成行


    const NOT_COMMUNITY = 0;
    const COMMUNITY = 1;

    const STATUS_DOING = 1;
    const STATUS_OVERDUE = 4;
    const STATUS_CLOSE = 3;
    const STATUS_DONE = 2;

    protected $table = 'party';

    protected $fillable =
        [
            'user_id',
            'image',
            'description',
            'details',
            'way',
            'city',
            'end_time',
            'sign_time',
            'people_no',
            'remaining_people_no',
            'date',
            'time',
            'site',
            'start_time',
            'isCommunity',
            'latitude',
            'longitude',
            'contact'
        ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * 参与者
     */
    public function participants()
    {
        return $this->hasMany(PartyOrder::class, 'party_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * 聚会评论
     */
    public function message()
    {
        return $this->hasMany(Message::class, 'party_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * 用户
     */
    public function user()
    {
        return $this->hasOne(User::Class, 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * 来点feel
     */
    public function goods()
    {
        return $this->hasMany(PartyGoods::Class, 'party_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * 来点标签
     */
    public function label()
    {
        return $this->hasMany(PartyLabel::Class, 'party_id', 'id');
    }

    /**
     * @param $uid
     * @return mixed
     * 获取用户举办的派对
     */
    static public function getUserHostParty($uid)
    {
        $party = self::withCount('participants')
            ->withCount('message')
            ->where('isDeleteAdmin', self::NOT_DELETE)
            ->where('isDeleteUser', self::NOT_DELETE)
            ->where('user_id', $uid)
            ->orderBy('created_at', 'desc')
            ->get();
        return $party;
    }
}