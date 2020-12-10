<?php


namespace App\Http\Controllers\Admin\Voyager;

use App\Http\Controllers\Admin\Voyager\VoyagerBaseController as BaseVoyagerController;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Facades\Voyager;

class CountryController extends  \TCG\Voyager\Http\Controllers\VoyagerBaseController
{

    public function paginate($items, $perPage = 25, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, ['path' => '/admin/categories']);

    }

    public function getChilds(&$arrLocationNew, $object) {
        $q = \App\Category::where('parent_id', '=', $object->id)->orderBy('name')->get();
        if (count($q) > 0) {
            foreach ($q as $city) {
                $arrLocationNew[] = $city;
                $this->getChilds($arrLocationNew, $city);
            }
        } else {
            return $arrLocationNew;
        }
    }



    /**
     * POST BRE(A)D - Store data.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        $name = json_decode($request->get('name_i18n'));
        $translate = new GoogleTranslate();
        if (!$name->de) {
            $name->de = $translate->setSource('en')->setTarget('de')->translate($name->en);
            $request->merge(['name_i18n' => json_encode($name)]);
        }
        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        event(new BreadDataAdded($dataType, $data));

        if (!$request->has('_tagging')) {
            if (auth()->user()->can('browse', $data)) {
                $redirect = redirect()->route("voyager.{$dataType->slug}.index");
            } else {
                $redirect = redirect()->back();
            }

            return $redirect->with([
                'message'    => __('voyager::generic.successfully_added_new')." {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        } else {
            return response()->json(['success' => true, 'data' => $data]);
        }
    }

}
