<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use TCG\Voyager\Traits\Translatable;
use Illuminate\Support\Facades\Cache;
use TCG\Voyager\Traits\Resizable;


class Style extends Model
{
    use Translatable;

    protected $translatable = ['name', 'description', 'meta_title', 'meta_description'];

    public static function display() {
        if (Cache::has('style_menu_'.App::getLocale())) {
            $styleMenu = Cache::get('style_menu_'.App::getLocale());
        } else {
            $styleMenu = Cache::remember('style_menu_'.App::getLocale(), \Carbon\Carbon::now()->addDays(30), function () {
                $arrStyles = array();
                foreach (\App\style::with('translations')->where('in_menu', 1)->orderBy('name', 'ASC')->get() as $menu) {
                    $arrStyles[$menu->slug] = (trim($menu->getTranslatedAttribute('name')));
                }

                return $arrStyles;
            });
        }

        return new \Illuminate\Support\HtmlString(
            \Illuminate\Support\Facades\View::make('menu.style', ['items' => $styleMenu])->render()
        );
    }  
}
