<?php


namespace App\Http\Controllers\Admin\Voyager;

use App\Exports\ProductExportSelect;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Facades\Voyager;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Symfony\Component\DomCrawler\Crawler;
use DiDom\Document;
use App\Exports\InteriorSelectProducts;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\Facades\Image as InterventionImage;
use Illuminate\Support\Facades\Storage;

class ProductController extends  \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    public function export()
    {
        return Excel::download(new InteriorSelectProducts, 'robers1659.xlsx');
        //return Excel::download(new ProductExportSelect, 'fine-art-lamps.xlsx');
    }


    public function import(Request $request) {
        ini_set('max_execution_time', 270000);

        Excel::import(new ProductImport, 'imports/robers.xlsx', 'public');
        echo "Done";
        exit;
    }

    public function regenThumbnal(Request $request) {
        ini_set('max_execution_time', 270000);
        $storagePath = str_replace('\\', '/', storage_path()).'/app/public_html/';
        $products = \App\Product::where('id', 6005)->get();

        $watermark = InterventionImage::make($storagePath.'products/001.png');
        $watermarkCropped = InterventionImage::make($storagePath.'products/003.png');
        $watermark150 = InterventionImage::make($storagePath.'products/004.png');

        foreach ($products as $product) {

            $images = json_decode($product->image);
            foreach ($images as $image) {
                $pathinfo = pathinfo($image);
                $fileName = $pathinfo['filename'].'.'.$pathinfo['extension'];
                $watermarkFileName = str_replace('.'.$pathinfo['extension'], '-watermark.'.$pathinfo['extension'], $fileName);
                $croppedFileName = str_replace('.'.$pathinfo['extension'], '-wcropped.'.$pathinfo['extension'], $fileName);
                $smallFileName = str_replace('.'.$pathinfo['extension'], '-small.'.$pathinfo['extension'], $fileName);

                if ($image == $product->main_photo) {
                    //continue;
                } else {
                    $imgOrigin = InterventionImage::make($storagePath.$image);

                    $imgSize = getimagesize($storagePath.$image);
                    $imgForCrop = InterventionImage::make($storagePath.$image);

                    $cropped = $imgForCrop->resize(400, 400, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $cropped->insert($watermark150, 'center');
                    $cropped->save($storagePath.$pathinfo['dirname'].DIRECTORY_SEPARATOR.$croppedFileName); 

                    if ($imgSize[0] < 700) {
                        $imgOrigin->insert($watermark150, 'center');
                    } else {
                        $imgOrigin->insert($watermark, 'center');
                    }
                    //$imgOrigin->insert($watermark, 'center');
                    $imgOrigin->save($storagePath.$pathinfo['dirname'].DIRECTORY_SEPARATOR.str_replace('.'.$pathinfo['extension'], '-watermark.'.$pathinfo['extension'], $fileName));                

                    //$cropped = InterventionImage::make($storagePath.$image);
                    // $cropped->resize(300, null);
                    //$cropped->crop(300, 300);
                    
                    /*$cropped->resize(null, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    });*/         
                    //$cropped->insert($watermark, 'center');         
                    /*if ($product->brand_id != 53 && $product->brand_id != 25) {
                        $cropped->insert($watermarkSmall, 'center');
                        $cropped->save($storagePath.$pathinfo['dirname'].DIRECTORY_SEPARATOR.$croppedFileName); 
                    }*/

    
    
                    //$small = InterventionImage::make($storagePath.$image);
                    //$small->resize(75, 75);
                    //$small->save($storagePath.$pathinfo['dirname'].DIRECTORY_SEPARATOR.$mallFileName);
                }
            }
            if ($product->main_photo) {
                //dd($product->main_photo);
                $croppedMainName = get_thumbnail($product->main_photo, 'cropped');
                //dd($storagePath.$croppedMainName);
                if (is_file($storagePath.$product->main_photo)) {
                    $imgSize = getimagesize($storagePath.$product->main_photo);


                    $imgOrigin = InterventionImage::make($storagePath.$product->main_photo);
                    $cropped = $imgOrigin->resize(400, 400, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $cropped->insert($watermark150, 'center');

                    /*if ($imgSize[0] < 700) {
                        $imgOrigin->insert($watermark150, 'center');
                    } else {
                        $imgOrigin->insert($watermark, 'center');
                    }*/                    
                    //$imgOrigin->insert($watermark150, 'center');
                    $cropped->save($storagePath.str_replace('-cropped', '-mwcropped', $croppedMainName));       
                } 
            }
            //dd($product->id);
        }
        echo "Done";
        exit;
    }

    public function getGroups(Request $request) {
        // $options = \App\Option::where('group_id', $request->get('groupId'))->get();
        $data['groups'] = \App\OptionGroup::all();
        $returnHTML = view('vendor.voyager.products.groups-select', compact('data'))->render();
        
        return response()->json(array(
            'success' => true,
            'html' => $returnHTML));
    }


    public function addOptions(Request $request) {
        // $options = \App\Option::where('group_id', $request->get('groupId'))->get();
        $data['options'] = \App\Option::where('group_id', $request->get('groupId'))->get();
        $data['selected'] = $request->get('ids') ? $request->get('ids') : array();
        $data['groupId'] = $request->get('groupId');
        $returnHTML = view('vendor.voyager.products.group-options', compact('data'))->render();
        
        return response()->json(array(
            'success' => true,
            'html' => $returnHTML));
    }

    public function getOptions(Request $request) {
        $data['options'] = \App\Option::whereIn('id', $request->get('ids'))->get();
        $data['exist'] = $request->get('extIds') ? $request->get('extIds') : array();
        $data['type'] = $request->get('type');
        $data['groupId'] = $request->get('groupId');
        if ($request->get('groupId')) {
            $data['group'] = \App\OptionGroup::where('id', $request->get('groupId'))->first();
        }
        $returnHTML = view('vendor.voyager.products.options-row', compact('data'))->render();
        return response()->json(array(
            'success' => true,
            'html' => $returnHTML));
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);
        //check folder
        $storagePath = str_replace('\\', '/', storage_path()).'/app/public_html/products/';
        if (!file_exists($storagePath.$id)) {
            Storage::disk('public')->makeDirectory('products/'.$id);
        }

        $ids = $request->get('ids');
        if (count($ids)) {
            $tmpOptions = array();
            $num = 0;
            foreach ($ids as $key => $values) {
                $prices = $request->get('oprice');
                $discounts = $request->get('odiscount');
                $articles = $request->get('article');
                $loadedPhotos = $request->get('optloadedphoto');
                $tmpOption[$num]['group_id'] = $key;
                $optionGroup = \App\OptionGroup::where('id', $key)->first();
                $tmpOption[$num]['label'] = $optionGroup->name;
                $files = $request->file('optphoto');
                //dd($files);
                foreach ($values as $t => $value) {
                    $tmpOption[$num]['selected'][] = $value;
                    $tmpOption[$num]['article'][] = $articles[$key][$t];
                    $tmpOption[$num]['price'][] = $prices[$key][$t];
                    $tmpOption[$num]['discount'][] = $discounts[$key][$t];
                    // if upload own file for option
                    if (@$files[$key][$t]) {
                        $imgName = $files[$key][$t]->getClientOriginalName();
                        $pathinfo = pathinfo($imgName);
                        $ext = @$pathinfo['extension'];
                        
                        //copy image
                        $isCopied = @copy($files[$key][$t]->getRealPath(), $storagePath.$id.'/'.$imgName);
                        if ($isCopied) {
                            $uploadedImage = $storagePath.$id.'/'.$imgName;
                            $croppedFileName = str_replace('.'.$ext, '-wcropped.'.$ext, $imgName);
                            $smallFileName = str_replace('.'.$ext, '-small.'.$ext, $imgName);
                            
                            $cropped = InterventionImage::make($uploadedImage);
                            $cropped->resize(400, 400);
                            $cropped->save($storagePath.$id.'/'.$croppedFileName); 
                            
                            $small = InterventionImage::make($uploadedImage);
                            $small->resize(75, 75);
                            $small->save($storagePath.$id.'/'.$smallFileName); 
                            
                            $tmpOption[$num]['photo'][] = 'products/'.$id.'/'.$imgName;
                        }
                    } else {
                        $tmpOption[$num]['photo'][] = $loadedPhotos[$key][$t];
                    }
                    //dd($request->file('optphoto')[0]);
                }
                $num++;
            }
        }
        $request->request->remove('ids'); 
        $request->request->remove('article'); 
        $request->request->remove('oprice');
        $request->request->remove('odiscount');
        $request->request->remove('optloadedphoto');
        $request->request->remove('optphoto');
        
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;

        $model = app($dataType->model_name);
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
            $model = $model->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $data = $model->withTrashed()->findOrFail($id);
        } else {
            $data = $model->findOrFail($id);
        }

        // Check permission
        $this->authorize('edit', $data);

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();
        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);
        
        $data->options = count($tmpOption) ? json_encode($tmpOption) : '';
        $data->slug = Str::slug($request->get('name')).'-'.Str::slug($request->sku);
        $data->save();

        event(new BreadDataUpdated($dataType, $data));

        if (auth()->user()->can('browse', app($dataType->model_name))) {
            $redirect = redirect()->route("voyager.{$dataType->slug}.index");
        } else {
            $redirect = redirect()->back();
        }

        return $redirect->with([
            'message'    => __('voyager::generic.successfully_updated')." {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }


    /**
     * POST BRE(A)D - Store data.
     *extends
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) {
        $slug = $this->getSlug($request);
        $ids = $request->get('ids');
        if (count($ids)) {
            $tmpOptions = array();
            $num = 0;
            foreach ($ids as $key => $values) {
                $prices = $request->get('oprice');
                $tmpOption[$num]['group_id'] = $key;
                $optionGroup = \App\OptionGroup::where('id', $key)->first();
                $tmpOption[$num]['label'] = $optionGroup->name;
                
                foreach ($values as $t => $value) {
                    $tmpOption[$num]['selected'][] = $value;
                    $tmpOption[$num]['price'][] = $prices[$key][$t];
                }
                $num++;
            }
        }
        $request->request->remove('ids'); 
        $request->request->remove('oprice');

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        /*$name = json_decode($request->get('name_i18n'));
        $slugR = json_decode($request->get('slug_i18n'));
        $translate = new GoogleTranslate();
        if (!$name->de) {
            $name->de = $translate->setSource('en')->setTarget('de')->translate($name->en);
            $slugR->de = Str::slug($name->de, '-');
            $request->merge(['name_i18n' => json_encode($name)]);
            $request->merge(['slug_i18n' => json_encode($slugR)]);
        }*/

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());
        
        $data->options = count($tmpOption) ? json_encode($tmpOption) : '';
        $data->slug = Str::slug($request->get('name'));

        $data->save();

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


