<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;


class Country extends Model
{
    use Translatable,
        Resizable;

    protected $translatable = ['name'];

    public function languages(){
        return $this->belongsToMany(Language::class);
    }

    public function currencies(){
        return $this->belongsToMany(Currency::class);
    }


    public function regionId(){
        return $this->belongsTo(Region::class);
    }

    public function region() {
        return $this->belongsTo('App\Region', 'region_id');
    }
}
