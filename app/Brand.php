<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use TCG\Voyager\Traits\Translatable;
use Illuminate\Support\Facades\Cache;
use TCG\Voyager\Traits\Resizable;

class Brand extends Model
{
    use Translatable,
    Resizable;

    protected $translatable = ['description', 'meta_title', 'meta_description']; 
    
    public function countryId(){
        return $this->belongsTo(Country::class);
    }

    public function country() {
        return $this->belongsTo('App\Country', 'country_id');
    }

    public static function display() {
        if (Cache::has('brand_menu_'.App::getLocale())) {
            $brandMenu = Cache::get('brand_menu_'.App::getLocale());
        } else {
            $brandMenu = Cache::remember('brand_menu_'.App::getLocale(), \Carbon\Carbon::now()->addDays(30), function () {
                $arrBrands = array();
                foreach (\App\Brand::with('translations')->where('in_menu', 1)->orderBy('name', 'ASC')->get() as $menu) {
                    $arrBrands[$menu->slug] = (trim($menu->getTranslatedAttribute('name')));
                }

                return $arrBrands;
            });
        }

        return new \Illuminate\Support\HtmlString(
            \Illuminate\Support\Facades\View::make('menu.brand', ['items' => $brandMenu])->render()
        );
    }

}
