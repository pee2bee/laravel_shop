<?php

namespace App\Models;

use App\Exceptions\InternalException;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProductSku
 *
 * @property int $id
 * @property string $title sku标题
 * @property string $description sku描述
 * @property string $price sku价格
 * @property int $stock sku库存
 * @property int $product_id 所属商品id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereCreatedAt( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereDescription( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereId( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku wherePrice( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereProductId( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereStock( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereTitle( $value )
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereUpdatedAt( $value )
 * @mixin \Eloquent
 */
class ProductSku extends Model {
    //
    protected $fillable = [
        'title',
        'description',
        'price',
        'stock',
        'product_id'
    ];

    public function product() {
        return $this->belongsTo( Product::class );
    }

    /**高并发减库存，而不是单独update改字段值
     *
     * @param $amount
     */
    public function decreaseStock( $amount ) {
        if ( $amount < 0 ) {
            throw new InternalException( '减库存不可小于0' );
        }
        // 方法来减少字段的值，该方法会返回影响的行数
        //通过返回影响的行数来判断是否减库存成功，如果不成功就说明库存不足
        return $this->where( 'id', $this->id )->where( 'stock', '>=', $amount )->decrement( 'stock', $amount );
    }

    public function addStock( $amount ) {
        if ( $amount < 0 ) {
            throw new InternalException( '加库存不可小于0' );
        }
        $this->increment( 'stock', $amount );
    }

}
