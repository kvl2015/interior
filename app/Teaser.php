<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;
use TCG\Voyager\Traits\Resizable;


class Teaser extends Model
{
    use Translatable,
        Resizable;

    protected $translatable = ['caption'];

    public function categoryId(){
        return $this->belongsTo(Category::class);
    }

    public function category() {
        return $this->belongsTo('App\Category', 'category_id');
    }

}
