<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;


class Language extends Model
{
    use Translatable,
        Resizable;

    protected $translatable = ['name'];
}
