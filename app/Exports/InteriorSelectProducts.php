<?php
namespace App\Exports;

// use App\Invoice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use DiDom\Document;
use Illuminate\Support\Str;

class InteriorSelectProducts implements FromCollection
{
    public function collection()
    {
        ini_set('max_execution_time', 270000);
        $num = 1;
        $productsData[0] = array(
            'active', 'visible', 'code', 'gtin', 'custom_url', 'handle',
            'title', 'subtitle', 'description', 'price', 'purchase_price',
            'compare_at_price', 'weight', 'mpn_code', 'asin_code', 'ebay_code',
            'isbn_code', 'length', 'width', 'height', 'vendor',
            'category', 'base_unit', 'base_quantity', 'base_quantity_sold', 'base_quantity_package',
            'base_perform_calculation', 'shipping_type_names', 'minimum_shipping_cost', 'tag_list', 'considers_stock',
            'shows_stock_amount', 'stock', 'minimum_stock', 'featured', 'new',
            'option_01', 'option_02', 'option_03', 'option_01_label', 'option_02_label', 'option_03_label',
            'created_at', 'updated_at', 'content_title_tag', 'content_meta_description', 'code_of_parent',
            'image_url_1', 'image_description_1',
            'image_url_2', 'image_description_2',
            'image_url_3', 'image_description_3',
            'image_url_4', 'image_description_4',
            'image_url_5', 'image_description_5',
            'image_url_6', 'image_description_6',
            'image_url_7', 'image_description_7',
            'image_url_8', 'image_description_8',
            'image_url_9', 'image_description_9',
            'image_url_10', 'image_description_10',
            'image_url_11', 'image_description_11',
            'image_url_12', 'image_description_12',
            'image_url_13', 'image_description_13',
            'image_url_14', 'image_description_14',
            'image_url_15', 'image_description_15'
        );
        //image_url_15	image_description_15
        for ($i = 4; $i<=4; $i++) {
            $link = 'https://www.select-interiorworld.com/at_de/moretti-luce?p='.$i;
            $link = 'https://www.select-interiorworld.com/de_de/robers?p='.$i;
            // $link = 'https://www.select-interiorworld.com/at_en/robers?p='.$i;
            $document = new Document($link, true);

            $productGrids = $document->find('div[id=product-wrapper]')[0]->find('li');
            $pCount = 0;
            foreach($productGrids as $key => $product) {
                $productLink = '';
                if (@$product->find('.product-item-name')[0]) {
                    if (@$product->find('.product-item-name')[0]->find('a')[0]) {
                        $productLink = $product->find('.product-item-name')[0]->find('a')[0]->getAttribute('href');
                    }
                }
                $mainPhoto = '';
                //$productLink = 'https://www.select-interiorworld.com/ua_de/robers/robers-wall-lamp-wl-3582.html';

                if ($productLink) {
                    if ($pCount++ > 6) {
                        //echo $pCount;exit;
                        //dd($productsData);
                        break;
                    }
                    $productPage = new Document($productLink, true);
                    if ($productPage) {
                        for($j=0;$j<77;$j++) {
                            $productsData[$num][$j] = '';
                        }
                        //active
                        $productsData[$num][0] = TRUE;
                        //visible
                        $productsData[$num][1] = TRUE;

                        //code
                        $sku = @$productPage->find('.sku')[0];
                        $productsData[$num][2] = $sku ? str_replace('SKU: ', '', $sku->text()) : '';
                        $productsData[$num][2] = Str::slug($productsData[$num][2]);
                        
                        //handle
                        $elLink = array_filter(explode('/', $productLink));
                        $productsData[$num][5] = $handle = str_replace('.html', '', trim($elLink[count($elLink) ]));
                        $productsData[$num][5] = Str::slug($productsData[$num][5]);

                        //title
                        $productsData[$num][6] = @$productPage->find('.product-name')[0] ?
                        $productPage->find('.product-name')[0]->text() : '';

                        $description = '';
                        if (@$productPage->find('.std')[0]) {
                            foreach (@$productPage->find('.std')[0]->find('p') as $p) {
                                $description .= '<p>' .$p->text(). '</p>';
                            }
                        }
                        if (@$productPage->find('table[id=product-attribute-specs-table]')[0]) {
                            $description .= '<table>';
                            foreach (@$productPage->find('table[id=product-attribute-specs-table]')[0]->find('tr') as $row) {
                                $description .= '<tr>';
                                $description .= '<th>'.$row->find('th')[0]->text().'</th>';
                                if (@$row->find('td')[0]->find('ul')[0]) {
                                    $description .= '<td><ul>';
                                    foreach ($row->find('td')[0]->find('ul')[0]->find('li') as $li) {
                                        $description .= '<li>'.($li->text()).'</li>';
                                    }
                                    $description .= '</ul></td>';
                                } else {
                                    $description .= '<td>'.$row->find('td')[0]->text().'</td>';
                                }
                                $description .= '</tr>';
                            }
                            $description .= '</table>';
                        }
                        $productsData[$num][7] = '';
                        //description
                        $productsData[$num][8] = $description;
                        //price
                        $productsData[$num][9] = @$productPage->find('.price')[0] ?
                        str_replace(array('€', '.', ','), array('', '', '.'), $productPage->find('.price')[0]->text()) : 0;
//dd(str_replace(array('€', '.', ','), array('', '', '.'), $productPage->find('.price')[0]->text()));
                        //compare price
                        $productsData[$num][11] = 0;
                        
                        

                        //$dimentionsFull = $productPage->find('.dimensions')[0];
                        /*if ($dimentionsFull) {
                            $dimention = explode('/', $dimentionsFull->find('.attribute-value')[0]->text());
                            $param = explode('x', str_replace('cm.:', '', $dimention[0]));
                            dd($dimentionsFull->find('.attribute-value')[0]->text());
                            //width
                            $productsData[$num][18] = trim(str_replace(array('H', 'W', 'D'), '', @$param[0]));
                            //height
                            $productsData[$num][19] = trim(str_replace(array('H', 'W', 'D'), '', @$param[1]));
                            //length
                            $productsData[$num][17] = trim(str_replace(array('H', 'W', 'D'), '', @$param[2]));
                        }*/
                        //category
                        $category = explode('/', $productsData[$num]['6']);
                        $productsData[$num][21] = trim($category[1]);
                        //base unit
                        $productsData[$num][22] = 'piece';
                        //base quantity
                        $productsData[$num][23] = 1;
                        //base quantity sold
                        $productsData[$num][24] = 1;
                        //base quantity package
                        $productsData[$num][25] = 1;
                        //base perform calculation
                        $productsData[$num][26] = FALSE;
                        //considers stock
                        $productsData[$num][30] = FALSE;
                        //shows_stock_amount
                        $productsData[$num][31] = FALSE;
                        //stock
                        $productsData[$num][32] = 1;
                        //featured
                        $productsData[$num][34] = FALSE;
                        //new
                        $productsData[$num][35] = FALSE;
                        $photos = array();
                        $photoNumUrl = 47;
                        if (@$productPage->find('.product-gallery-carousel')[0]) {
                            foreach ($productPage->find('.product-gallery-carousel')[0]->find('img') as $image) {
                                $productsData[$num][$photoNumUrl] = $image->getAttribute('src');
                                $productsData[$num][$photoNumUrl+1] = '';
                                $photoNumUrl = $photoNumUrl + 2;
                            }
                        }
                        //start options
                        $options = array();
                        $tmpOptions = array();
                        $script = $productPage->find('script[type=text/x-magento-init]');
                        
                        foreach ($script as $x) {
                            if (strpos($x->text(), 'swatch-options')) {
                                $jData = (array) (json_decode($x->text()));
                                $jRenderer = (array) ($jData["[data-role=swatch-options]"]);
                                // dd($jRenderer["Magento_Swatches/js/swatch-renderer"]->jsonSwatchConfig);
                                $swatchOptions = $jRenderer["Magento_Swatches/js/swatch-renderer"]->jsonSwatchConfig;
                                $k = 0;
                                foreach ($jRenderer["Magento_Swatches/js/swatch-renderer"]->jsonConfig->attributes as $t => $attr) {
                                    $optionsTmp = array();
                                    $tmpOptions[$k]['label'] = $attr->label;
                                    $tmpOptions[$k]['code'] = $attr->code;
                                    foreach ($attr->options as $m => $opt) {
                                        $optId = $opt->id;
                                        $tmpOptions[$k]['option'][] = array(
                                            'thumb' => @$swatchOptions->$t->$optId->thumb,
                                            'name' => $opt->label,
                                            'id' => $opt->id
                                        );
                                        //$optionsTmp[] = array($opt->id, $opt->label, $t, $attr->label, $attr->code, @$swatchOptions->$t->$optId->value, @$swatchOptions->$t->$optId->thumb);
                                    }
                                    $k++;
                                    //$finalOptions[] = $optionsTmp;
                                }
                                break;
                            } elseif (strpos($x->text(), '#product_addtocart_form')) {
                                $jData = (array) (json_decode($x->text()));
                                $jRenderer = (array) ($jData["#product_addtocart_form"]);
                                if (isset($jRenderer["configurable"]->spConfig)) {
                                    // echo $productLink;
                                    // dd($jRenderer["configurable"]->spConfig->attributes);
                                    $optionsTmp = array();$k = 0;
                                    foreach ($jRenderer["configurable"]->spConfig->attributes as $t => $attr) {
                                        $optionsTmp = array();
                                        $tmpOptions[$k]['label'] = $attr->label;
                                        $tmpOptions[$k]['code'] = $attr->code;
                                            //$productsData[$num]['options'][$t] = array($attr->code, $attr->label);
                                        foreach ($attr->options as $m => $opt) {
                                            //$optionsTmp[] = array($opt->id, $opt->label, $attr->id, $attr->label, $attr->code);
                                            $tmpOptions[$k]['option'][] = array(
                                                'name' => $opt->label,
                                                'id' => $opt->id
                                            );
                                            //$productsData[$num]['options'][$t][] = array($opt->code, $opt->label);
                                        }
                                        // dd($optionsTmp);
                                        //$finalOptions[] = $optionsTmp;
                                    }
                                    break;
                                }
                            }
                        }
//dd($tmpOptions);
                        
                        $childCount = 0;$optSet = array();
                        foreach ($tmpOptions as $key1 => $option) {
                            $optionCode = $option['code'];
                            if (strpos($optionCode, 'voltage')) {
                                //dd($option['code']);
                                //$productsData[$num][$optVal++] = $option['option'][0]['name'];
                                //$productsData[$num][$optNum++] = $option['label'];
                                unset($tmpOptions[$key1]);
                            } else {
                                $optSet[$option['label']] = $option['option'];
                                $childCount += count($option['option']);
                            }
                        }
//dd($tmpOptions);
                        $optKey = array_keys($optSet);
                        $tmpOptKey = array();
                        $_optNum = 0;
                        //dd($optSet[$optKey[0]]);
                        foreach ($optSet[$optKey[0]] as $loption) {
                            if (isset($optKey[1])) {
                                
                                foreach($optSet[$optKey[1]] as $loption1) {
                                    if (isset($optKey[2])) {
                                        foreach($optSet[$optKey[2]] as $loption2) {
                                            $tmpOptKey[$_optNum][$optKey[0]] = $loption['name'];
                                            $tmpOptKey[$_optNum][$optKey[1]] = $loption1['name'];
                                            $tmpOptKey[$_optNum][$optKey[2]] = $loption2['name'];
                                            $_optNum++;
                                        }
                                    } else {
                                        $tmpOptKey[$_optNum][$optKey[0]] = $loption['name'];
                                        $tmpOptKey[$_optNum][$optKey[1]] = $loption1['name'];
                                        $_optNum++;
                                    }
                                }
                                
                            } else {
                                $tmpOptKey[$_optNum][$optKey[0]] = $loption['name'];
                                $_optNum++;
                            }
                        }
                        $numParent = $productsData[$num][2];
                        $optNum = 38; $optVal = 35; $codeOfParent = $productsData[$num][2];$t = 1;
//dd($tmpOptKey);
                        foreach ($tmpOptKey as $key1 => $value) {
                            if ($key1 !=0 ) {
                                ksort($productsData[$num]);
                                $productsData[++$num] = $productsData[$num - 1];
                                //$optNum = 39; $optVal = 36;
                                //code of parent
                                $productsData[$num][46] = $codeOfParent;
                            }
//dd($value);
                            $stringForSlug = '';
                            $optNum = 39; $optVal = 36;
                            foreach($value as $_key => $_value) {
                                if (strpos($_value, 'inch')) {
                                    $productsData[$num][$optVal++] = substr($_value, 0, (strpos($_value, 'inch') - 3)).')';
                                    $stringForSlug .= substr($_value, 0, (strpos($_value, 'inch') - 3)).')'.'-';
                                    //dd(substr($_value, 0, (strpos($_value, 'inch') - 3)));
                                } else {
                                    $productsData[$num][$optVal++] = $_value;
                                    $stringForSlug .= $_value.'-';
                                }
                                $productsData[$num][$optNum++] = $_key;
                                //$stringForSlug .= $_value.'-';
                            }
                            
                            if ($key1 != 0) {
                                $productsData[$num][39] = '';
                                $productsData[$num][40] = '';
                                $productsData[$num][41] = '';
                                //code & handle
                                //dd($stringForSlug);
                                $productsData[$num][5] = $handle.'-'.Str::slug(substr($stringForSlug, 0, -1));
                                $productsData[$num][2] = $numParent.'-'.Str::slug(substr($stringForSlug, 0, -1));
                                
                            }
                    }
//dd($productsData);
                        
                        //dd($productsData);
                        //dd($childCount);
    
                        $num++;
//dd($productsData);
                    }
                }
            }
        }
//dd($productsData);
        return new Collection($productsData);
    }

    function objectToArray($obj)
    {
        $reaged = (array)$obj;
        foreach($reaged as $key => &$field){
            if(is_object($field))$field = $this->objectToArray($field);
        }
        return $reaged;
    }
}
