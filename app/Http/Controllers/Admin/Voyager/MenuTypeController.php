<?php

namespace App\Http\Controllers\Admin\Voyager;


use Illuminate\Http\Request;
use App\Http\Requests;
// use App\Page;

use Illuminate\Support\Facades\Cache;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Facades\Voyager;

use Importer;
use App\Imports\CategoryImport;
use Maatwebsite\Excel\Facades\Excel;


class MenuTypeController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    function setOrder($id, Request $request) {

        // Check permission
        $this->authorize('edit', app('App\MenuType'));

        $pages = array();
        foreach (\App\Page::get() as $page) {
            $pages[$page->id] = $page->title;
        }
        $menu = \App\MenuType::where('id','=', $id)->first();

        $arrMenus = array();
        if ($menu->order) {
            foreach (json_decode($menu->order) as $key => $_menu) {
                $_menu->title = $pages[$_menu->id];
                $arrMenus[] = $_menu;
                unset($pages[$_menu->id]);
            }
        }


        $view = 'voyager::bread.order';

        if (view()->exists("voyager::menu-types.order")) {
            $view = "voyager::menu-types.order";
        }

        return Voyager::view($view, compact(
            'pages',
            'menu',
            'arrMenus'
        ));
    }

    function updatePageOrder($id, Request $request) {

        // Check permission
        $this->authorize('edit', app('App\MenuType'));
        $menu = \App\MenuType::where('id','=', $id)->first();
        $menu->order = $request->get('nestable2-output');
        $menu->save();

        $redirect = redirect()->route("voyager.menu-types.index");
        return $redirect->with([
            'message'    => __('voyager::generic.successfully_updated'),
            'alert-type' => 'success',
        ]);

        //dd($request->get('nestable2-output'));
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
