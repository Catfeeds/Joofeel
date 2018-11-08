<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 17:11
 */

namespace App\Models\Goods;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    const SHELVES = 0;
    const NOT_SHELVES = 1;

    const DEFAULT_SORT = 0; //默认排序(按照created_at)
    const PRICE_ASC_SORT = 1; //价格升序
    const PRICE_DESC_SORT = 2; //价格降序

    protected $table = 'goods';

    protected $fillable =
        [
            'goods_id',
            'category_id',
            'type',
            'flavor',
            'goods_id',
            'stock',
            'name',
            'delivery_place',
            'country' ,
            'purchase_price',
            'logistics_standard',
            'cost_price',
            'reference_price',
            'price',
            'sale_price',
            'recommend_reason',
            'notice',
            'channels',
            'shop',
            'purchase_address',
            'thu_url',
            'cov_url',
            'det_url',
            'state',
            'updated_at',
            'created_at'
        ];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * 标签
     */
    public function label(){
        return $this->hasMany(GoodsLabel::class,'goods_id','id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(){
        return $this->belongsTo(GoodsCategory::class,'category_id','id');
    }
}