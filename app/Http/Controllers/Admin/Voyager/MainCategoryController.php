<?php
namespace App\Http\Controllers\Admin\Voyager;

use Illuminate\Http\Request;
use App\Http\Requests;
// use App\Page;

use Illuminate\Support\Facades\Cache;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Facades\Voyager;



class MainCategoryController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    function index(Request $request) {

        // Check permission
        $this->authorize('edit', app('App\MainCategory'));

        $categories = array();
        foreach (\App\Category::whereNotNull('image')->get() as $category) {
            $categories[$category->id] = array($category->name, $category->image);
        }
        $menu = \App\MainCategory::where('id','=', 1)->first();

        $arrCategories = array();
        if ($menu->order) {
            foreach (json_decode($menu->order) as $key => $_menu) {
                $_menu->name = $categories[$_menu->id];
                $arrCategories[] = $_menu;
                unset($categories[$_menu->id]);
            }
        }

        $view = "voyager::main-categories.browse";

        return Voyager::view($view, compact(
            'categories',
            'menu',
            'arrCategories'
        ));
    }

    function updatePageCategory($id, Request $request) {

        // Check permission
        $this->authorize('edit', app('App\MainCategory'));
        $menu = \App\MainCategory::where('id','=', 1)->first();
        $menu->order = $request->get('nestable2-output');
        $menu->save();

        $redirect = redirect()->route("voyager.main-categories.index");
        return $redirect->with([
            'message'    => __('voyager::generic.successfully_updated'),
            'alert-type' => 'success',
        ]);
    }

    public function cacheClear(Request $request) {
        Cache::flush();
        return redirect()
            ->route("voyager.pages.index")
            ->with([
                'message'    => "Кеш удален",
                'alert-type' => 'success',
            ]);
    }
}
