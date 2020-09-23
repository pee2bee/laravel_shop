<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    //
    protected $fillable = [
        'user_id', 'product_sku_id', 'amount'
    ];

    public function user(  ) {
        return $this->belongsTo(User::class,'user_id');
    }

    public function productSku(  ) {
        return $this->belongsTo(ProductSku::class,'product_sku_id');
    }
}
