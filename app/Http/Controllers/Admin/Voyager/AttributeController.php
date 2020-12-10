<?php


namespace App\Http\Controllers\Admin\Voyager;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Facades\Voyager;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Symfony\Component\DomCrawler\Crawler;
use DiDom\Document;
use Maatwebsite\Excel\Facades\Excel;


class AttributeController extends  \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    public function index(Request $request)
    {
        /*$values = DB::table('eav_attribute_option_value')->where('store_id', 2)->get();
        foreach ($values as $value) {
            $attr = \App\AttributeOption::where('option_id', $value->option_id)->first();
            $attr->value_de = $value->value;
            $attr->save();
        }*/
        // $productsData = Cache::get('lighting_25');
        // dd($productsData);

        ini_set('max_execution_time', 270000);
        $num = 13;
        for ($i = 1; $i<=1; $i++) {
            $link = 'https://www.vaughandesigns.com/lighting/table-lamps?limit=24&sort=recommended';
            $document = new Document($link, true);

            $productGrids = $document->find('.product-listing__item-wrapper');
            $productsData = array();
            foreach($productGrids as $key => $product) {
                // parse product info
                $productLink = 'https://www.vaughandesigns.com'.$product->find('.product-listing__item')[0]->getAttribute('href');
                //$productLink = 'https://www.vaughandesigns.com/lighting/table-lamps/aswan-ceramic-table-lamp/TC0061.XX';

                $productPage = new Document($productLink, true);

                // get full information about product
                $productsData[$key]['name'] = @$productPage->find('.product-details__title')[0] ?
                    $productPage->find('.product-details__title')[0]->text() : '';
                $productsData[$key]['code'] = @$productPage->find('.product-details__code')[0] ?
                    $productPage->find('.product-details__code')[0]->text() : '';
                $productsData[$key]['description'] = @$productPage->find('.product-details__text')[0] ?
                    $productPage->find('.product-details__text')[0]->text() : '';
                $productsData[$key]['photo'] = @$productPage->find('.product-details__photo')[0] ?
                    $productPage->find('.product-details__photo')[0]->find('img')[0]->getAttribute('src') : '';

                //check for select
                //dd($productPage->find('.product-details__option')[0]->find('option'));
                $options = $productPage->find('.product-details__option')[0]->find('option');
                //dd($options);
                if (count($options)) {
                    foreach ($options as $option) {
                        $productsData[$key]['options'][] = $option->text();
                    }
                } else {
                    $options = $productPage->find('.product-details__option')[0]->text();
                    $productsData[$key]['options'] = trim($options);
                }
                $productsData[$key]['categories'] = 'table_lamps';
                //dd($productsData);
                //exit;
                /*$productsData[$key]['id'] = @$productPage->find('input[name=product]')[0] ?
                    $productPage->find('input[name=product]')[0]->getAttribute('value') : '';
                $productsData[$key]['sku'] = @$productPage->find('.sku')[0] ?
                    $productPage->find('.sku')[0] ->first('span')->text() : '';
                $productsData[$key]['image_base'] = @$productPage->find('.product-image-base')[0] ?
                    $productPage->find('.product-image-base')[0]->find('img')[0]->getAttribute('src') : '';
                // dd($productsData);

                $productsData[$key]['brand'] = @$productPage->find('.brand-name')[0] ?
                    $productPage->find('.brand-name')[0]->first('a')->text() : '';
                $productsData[$key]['collection'] = @$productPage->find('.collection-name')[0] ?
                    $productPage->find('.collection-name')[0]->first('a')->text() : '';
                $productsData[$key]['designer'] = @$productPage->find('.designer-name')[0] ?
                    $productPage->find('.designer-name')[0]->first('a')->text() : '';
                $productsData[$key]['manufacture'] = @$productPage->find('.manufacturer-name')[0] ?
                    $productPage->find('.manufacturer-name')[0]->text() : '';
                $productsData[$key]['stock'] = @$productPage->find('.amstockstatus')[0] ?
                    $productPage->find('.amstockstatus')[0]->text() : '';

                $script = $productPage->find('script[type=text/x-magento-init]');
                foreach ($script as $x) {
                    if (strpos($x->text(), 'swatch-options')) {
                        $jData = (array) (json_decode($x->text()));
                        $jRenderer = (array) ($jData["[data-role=swatch-options]"]);
                        $productsData[$key]['options_config'] = $jRenderer["Magento_Swatches/js/swatch-renderer"]->jsonConfig;
                        $productsData[$key]['image_tmp'] = $jRenderer["Magento_Swatches/js/swatch-renderer"]->jsonConfig->images;
                        break;
                    } elseif (strpos($x->text(), '#product_addtocart_form')) {
                        $jData = (array) (json_decode($x->text()));
                        $jRenderer = (array) ($jData["#product_addtocart_form"]);
                        if (isset($jRenderer["configurable"]->spConfig)) {
                            $productsData[$key]['options_config'] = $jRenderer["configurable"]->spConfig;
                            $productsData[$key]['image_tmp'] = $jRenderer["configurable"]->spConfig->images;
                            break;
                        }
                    }
                }

                $productsData[$key]['meta_description'] = @$productPage->find('meta[name=description]')[0] ?
                    $productPage->find('meta[name=description]')[0]->getAttribute('content') : '';
                $productsData[$key]['meta_title'] = @$productPage->find('meta[name=title]')[0] ?
                    $productPage->find('meta[name=title]')[0]->getAttribute('content') : '';
                $productsData[$key]['robots'] = @$productPage->find('meta[name=robots]')[0] ?
                    $productPage->find('meta[name=robots]')[0]->getAttribute('content') : '';
                //$productsData[$key]['description'] = $productPage->find('.product-descr-tab-content-section')[0]->text();
                $productsData[$key]['price'] = @$productPage->find('.price')[0] ?
                    str_replace('€', '', $productPage->find('.price')[0]->text()) : 0;*/

                // dd($productsData);
                Cache::put('lighting_'.$i, $productsData, \Carbon\Carbon::now()->addDays(5));
            }
        }
        dd($productsData);

        //Cache::put('lighting_'.$num, $productsData, \Carbon\Carbon::now()->addDays(5));
        echo "Done for page 46 - 55 ";

        //save to cache lingthning


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
