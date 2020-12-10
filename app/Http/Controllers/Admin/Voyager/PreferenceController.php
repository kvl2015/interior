<?php

namespace App\Http\Controllers\Admin\Voyager;

use App\Http\Controllers\Admin\Voyager\VoyagerBaseController as BaseVoyagerController;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\PreferenceProduct;
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Cache;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Facades\Voyager;

use Illuminate\Support\Facades\DB;


class PreferenceController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    function addLookbook($id, Request $request) {
        $preference = \App\Preference::where('id', $id)->first();
        $looks = \App\PreferenceProduct::where('owner_id', $id)->get();

        return Voyager::view('voyager::preferences.lookbook', compact(
            'preference',
            'id',
            'looks'
        ));
    }


    public function updateLook($id, Request $request) {
        DB::table('preference_products')->where('owner_id', '=', $id)->delete();
        $data = $request->all();
        if (count($data['product'])) {
            foreach ($data['connected'] as $key => $value) {
                if ($value > 0) {
                    $preferenceProduct = new PreferenceProduct();
                    $preferenceProduct->owner_id = $id;
                    $preferenceProduct->product_id = $value;
                    $preferenceProduct->pos_x = $data['posX'][$key];
                    $preferenceProduct->pos_y = $data['posY'][$key];
                    $preferenceProduct->save();
                }
            }
        }

        $redirect = redirect()->route("voyager.preferences.index");
        return $redirect->with([
            'message'    => __('voyager::generic.successfully_updated'),
            'alert-type' => 'success',
        ]);

    }


    function getProducts(Request $request) {
        $query = \App\Product::select('products.*');
        $query->where('active', 1);
        $query->whereTranslation('name', 'LIKE', '%'.$request->get('query').'%')
            ->orWhere('sku', 'LIKE', '%'.$request->get('query').'%')
            ->orWhere('slug', 'LIKE', '%'.$request->get('query').'%');
        $arrData = array();
        foreach ($query->get() as $product) {
            $arrData[] = array(
                'id'    => $product->id,
                'value' => $product->name,
                'label' => $product->name
            );
        }

        return response()->json($arrData);

    }

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

}
