<?php


namespace App\Http\Controllers\Admin\Voyager;


use App\Http\Controllers\Admin\Voyager\VoyagerBaseController as BaseVoyagerController;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Category;

use Illuminate\Support\Facades\DB;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Facades\Voyager;

use Importer;
use App\Imports\CategoryImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;



class CategoryController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    function getTree(&$arrCategories, $location, $level) {
        $dotSeparator = ' ';
        for($i=0;$i<$level;$i++) $dotSeparator .= '-';
        if (count($location->childs)) {
            foreach(\App\Category::where('parent_id', '=', $location->id)->orderBy('created_at')->get() as $_location) {
                $arrCategories[$_location->id] = $dotSeparator.' '.$_location->getTranslatedAttribute('name');
                $this->getTree($arrCategories, $_location, $level+1);
            }
        } else {
            return $arrCategories;
        }
    } 
     
    /*public function index(Request $request)
    {
        $arrCategories = array();
        
               
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);
        //$q = \App\Category::where('parent_id', '=', NULL)->orderBy('name')->get();

        

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];


        $searchNames = [];
        if ($dataType->server_side) {
            $searchable = SchemaManager::describeTable(app($dataType->model_name)->getTable())->pluck('name')->toArray();
            $dataRow = Voyager::model('DataRow')->whereDataTypeId($dataType->id)->get();
            foreach ($searchable as $key => $value) {
                $displayName = $dataRow->where('field', $value)->first()->getTranslatedAttribute('display_name');
                $searchNames[$value] = $displayName ?: ucwords(str_replace('_', ' ', $value));
            }
        }

        $orderBy = $request->get('order_by', $dataType->order_column);
        $sortOrder = $request->get('sort_order', null);
        $usesSoftDeletes = false;
        $showSoftDeleted = false;

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $query = $model->{$dataType->scope}();
            } else {
                $query = $model::select('*');
            }

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            
            // If a column has a relationship associated with it, we do not want to show that field
            //$this->removeRelationshipField($dataType, 'browse');

            if ($search->value != '' && $search->key && $search->filter) {
                $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';
                $query->where($search->key, $search_filter, $search_value);
            } else {
                $query->whereNull('parent_id');
                $query->orderBy('name', 'ASC');
            }

            if ($orderBy && in_array($orderBy, $dataType->fields())) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'desc';
                $dataTypeContent = call_user_func([
                    $query->orderBy($orderBy, $querySortOrder),
                    $getter,
                ]);
            } elseif ($model->timestamps) {
                $dataTypeContent = call_user_func([$query->latest($model::CREATED_AT), $getter]);
            } else {
                $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
            }

            // Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            // If Model doesn't exist, get data from table name
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
            $model = false;
        }

        // Check if BREAD is Translatable
        if (($isModelTranslatable = is_bread_translatable($model))) {
            $dataTypeContent->load('translations');
        }

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        // Check if a default search key is set
        $defaultSearchKey = $dataType->default_search_key ?? null;

        // Actions
        $actions = [];
        //if (!empty($dataTypeContent->first())) {
        foreach (Voyager::actions() as $action) {
            $action = new $action($dataType, $dataTypeContent->first());

            if ($action->shouldActionDisplayOnDataType()) {
                $actions[] = $action;
            }
        }
        //}
        // Define showCheckboxColumn
        $showCheckboxColumn = false;

        $parentLocation = \App\Category::where('parent_id', '=', NULL)->orderBy('id')->get();
        $arrLocation = array();
        foreach ($parentLocation as $location) {
            $arrLocation[] = array('location' => $location, 'level' => 0);
            $childs = \App\Category::where('parent_id', '=', $location->id)->orderBy('id')->get();
            foreach ($childs as $child) {
                $arrLocation[] = array('location' => $child, 'level' => 1);
                if ($child->childs()) {
                    $_childs = \App\Category::where('parent_id', '=', $child->id)->orderBy('id')->get();
                    foreach($_childs as $_child) {
                        $arrLocation[] = array('location' => $_child, 'level' => 2);
                    }
                }
            }
        }
//dd($arrLocation);
        $orderColumn = 'name';
        $dataTypeContent = $this->paginate($arrLocation, 25, $request->get('page'));

        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }

        return Voyager::view($view, compact(
            'actions',
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'orderColumn',
            'sortOrder',
            'searchNames',
            'isServerSide',
            'defaultSearchKey',
            'usesSoftDeletes',
            'showSoftDeleted',
            'showCheckboxColumn',
            'arrCategories'
        ));
    }*/

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
     * Order BREAD items.
     *
     * @param string $table
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function order(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('edit', app($dataType->model_name));

        if (!isset($dataType->order_column) || !isset($dataType->order_display_column)) {
            return redirect()
                ->route("voyager.{$dataType->slug}.index")
                ->with([
                    'message'    => __('voyager::bread.ordering_not_set'),
                    'alert-type' => 'error',
                ]);
        }

        $model = app($dataType->model_name);
        $results = $model->whereNull('parent_id')->orderBy($dataType->order_column)->get();

        $display_column = $dataType->order_display_column;

        $view = 'voyager::bread.order';

        if (view()->exists("voyager::$slug.order")) {
            $view = "voyager::$slug.order";
        }

        return Voyager::view($view, compact(
            'dataType',
            'display_column',
            'results'
        ));
    }


    public function update_order(Request $request) {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('edit', app($dataType->model_name));

        $model = app($dataType->model_name);

        $order = json_decode($request->input('order'));
        $this->orderChild($order, null);
    }


    private function orderChild(array $menuItems, $parentId)
    {
        foreach ($menuItems as $index => $menuItem) {
            $item = Category::where('id', '=', $menuItem->id)->first();
            //dd($menuItem);
            $item->order = $index + 1;
            $item->parent_id = $parentId;
            $item->save();

            if (isset($menuItem->children)) {
                $this->orderChild($menuItem->children, $item->id);
            }
        }
    }

    public function update(Request $request, $id) {

        $categoryData = Category::find($id);
        if ($categoryData->parent_id != $request->get('parent_id')) {
            $order = Category::where('parent_id', $request->get('parent_id'))->max('order') + 1;
            $request->merge([
                'order' => $order,
            ]);
        }

        return parent::update($request,$id);
    }


    public function store(Request $request) {
        $order = Category::where('parent_id', $request->get('parent_id'))->max('order') + 1;
        $request->merge([
            'order' => $order,
        ]);

        return parent::store($request);
    }

}
