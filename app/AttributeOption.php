<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class AttributeOption extends Model
{
    // use Translatable;
    // protected $translatable = ['value'];

    public function attributeId(){
        return $this->belongsTo(Attribute::class);
    }

    public function attribute() {
        return $this->belongsTo('App\Attribute', 'attribute_id');
    }
}
