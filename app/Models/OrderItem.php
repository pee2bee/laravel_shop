<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderItem
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $product_sku_id
 * @property int $amount
 * @property string $price
 * @property int|null $rating
 * @property string|null $review
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductSku $productSku
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereAmount( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereCreatedAt( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereId( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereOrderId( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem wherePrice( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereProductId( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereProductSkuId( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereRating( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereReview( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereReviewedAt( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereUpdatedAt( $value )
 * @mixin \Eloquent
 */
class OrderItem extends Model {
    //可批量填充字段
    protected $fillable = [ 'amount', 'price', 'rating', 'review', 'reviewed_at' ];
    //dates里面填写的字段，会自动调整时间
    //格式化成Carbon类
    protected $dates = [ 'reviewed_at' ];

    //关联模型product
    public function product() {
        return $this->belongsTo( Product::class );
    }

    //关联模型 productSku
    public function productSku() {
        return $this->belongsTo( ProductSku::class );
    }

    //关联模型 order
    public function order() {
        return $this->belongsTo( Order::class );
    }
}
