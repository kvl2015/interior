<?php


namespace App\Http\Controllers\Admin\Voyager;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Str;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Facades\Voyager;
use Stichoza\GoogleTranslate\GoogleTranslate;

class RoomController extends  \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    /**
     * POST BRE(A)D - Store data.
     *extends
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
        $slugR = json_decode($request->get('slug_i18n'));
        $translate = new GoogleTranslate();
        if (!$name->de) {
            $name->de = $translate->setSource('en')->setTarget('de')->translate($name->en);
            $slugR->de = Str::slug($name->de, '-');
            $request->merge(['name_i18n' => json_encode($name)]);
            $request->merge(['slug_i18n' => json_encode($slugR)]);
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
