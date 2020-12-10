<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class PreferenceProduct extends Model
{
    public function productId(){
        return $this->belongsTo(Product::class);
    }

    public function product() {
        return $this->belongsTo('App\Product', 'product_id');
    }
}
