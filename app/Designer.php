<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use TCG\Voyager\Traits\Translatable;


class Designer extends Model
{
    use Translatable;

    protected $translatable = ['description', 'meta_title', 'meta_description']; 

    public static function display() {
        if (Cache::has('designer_menu_'.App::getLocale())) {
            $designerMenu = Cache::get('designer_menu_'.App::getLocale());
        } else {
            $designerMenu = Cache::remember('designer_menu_'.App::getLocale(), \Carbon\Carbon::now()->addDays(30), function () {
                $arrDesigners = array();
                foreach (\App\Designer::where('in_menu', 1)->orderBy('name', 'ASC')->get() as $menu) {
                    $arrDesigners[$menu->slug] = (trim($menu->name));
                }

                return $arrDesigners;
            });
        }

        return new \Illuminate\Support\HtmlString(
            \Illuminate\Support\Facades\View::make('menu.designer', ['items' => $designerMenu])->render()
        );
    }    
}
