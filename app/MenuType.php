<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;
use Illuminate\Support\Facades\Cache;

class MenuType extends Model
{
    use Translatable;

    protected $translatable = ['name'];

    public static function display($menuName, $type = null, array $options = []) {

        if (Cache::has('frontend_menu_'.$menuName)) {
            $pageMenu = Cache::get('frontend_menu_'.$menuName);
        } else {
            $pageMenu = Cache::remember('frontend_menu_'.$menuName, \Carbon\Carbon::now()->addDays(30), function () use ($menuName) {
                $menuItems = array();

                $menuType = \App\MenuType::where('slug', '=', $menuName)->first();

                $query = \App\Page::select();
                foreach (json_decode($menuType->order) as $menu) {
                    $menuItems[] = \App\Page::where('id', '=', $menu->id)->first();
                }
                $menus['name'] = $menuType->getTranslatedAttribute('name');
                $menus['links'] = $menuItems;
                return $menus;
            });
        }
//dd($pageMenu);
        return new \Illuminate\Support\HtmlString(
            \Illuminate\Support\Facades\View::make('menu.site', ['items' => $pageMenu])->render()
        );
    }
    
}
