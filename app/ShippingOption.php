<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;



class ShippingOption extends Model
{
    use Translatable;

    protected $translatable = ['name'];
}
