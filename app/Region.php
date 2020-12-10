<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;


class Region extends Model
{
    use Translatable;

    protected $translatable = ['name'];
}
