<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;


class Option extends Model
{
    use Translatable,
    Resizable;

    protected $translatable = ['name'];    
    
    public function groupId(){
        return $this->belongsTo(OptionGroup::class);
    }

    public function group() {
        return $this->belongsTo('App\OptionGroup', 'group_id');
    }    
}
