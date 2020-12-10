<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Preference extends Model
{
    public function look() {
        return $this->hasMany('App\PreferenceProduct', 'owner_id', 'id');
    }    
}
