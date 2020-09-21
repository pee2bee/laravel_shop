<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    use DefaultDatetimeFormat;
    protected $fillable = [
        'title','description', 'image', 'on_sold', 'rating', 'sold_count', 'view_count', 'price'
    ];

    public function productSkus(  ) {
        return $this->hasMany(ProductSku::class);
    }



}
