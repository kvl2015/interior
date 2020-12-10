<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\Page as PageResource;


class Page extends Model
{
    use Translatable;
    protected $translatable = ['menu_name', 'title', 'body', 'meta_title', 'meta_keyword', 'meta_description'];

    public static function display() {

        if (Cache::has('page_menu')) {
            $pageMenu = Cache::get('page_menu');
        } else {
            $pageMenu = Cache::remember('page_menu', \Carbon\Carbon::now()->addDays(30), function () {
                return $menuItems = \App\Page::with('translations')->where([
                    ['active', '=', '1'],
                    ['top_menu', '=', '1'],
                ])->orderBy('order', 'asc')->get();
                //return $menuItems;
            });
        }

        return new \Illuminate\Support\HtmlString(
            \Illuminate\Support\Facades\View::make('menu.page', ['items' => $pageMenu])->render()
        );
    }
}
