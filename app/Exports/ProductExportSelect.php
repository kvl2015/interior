<?php
namespace App\Exports;

// use App\Invoice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use DiDom\Document;

class ProductExportSelect implements FromCollection
{
    public function collection()
    {
        ini_set('max_execution_time', 270000);
        $num = 0;
        $productsData = array();
        for ($i = 1; $i<=1; $i++) {
            $link = 'https://www.select-interiorworld.com/at_en/moretti-luce?p='.$i;
            // $link = 'https://www.select-interiorworld.com/at_en/robers?p='.$i;
            $document = new Document($link, true);

            $productGrids = $document->find('div[id=product-wrapper]')[0]->find('li');

            foreach($productGrids as $key => $product) {
                $productLink = '';
                if (@$product->find('.product-item-name')[0]) {
                    if (@$product->find('.product-item-name')[0]->find('a')[0]) {
                        $productLink = $product->find('.product-item-name')[0]->find('a')[0]->getAttribute('href');
                    }
                }
                $mainPhoto = '';
                /*if (@$product->find('.product-list-image-slider-item')) {
                    foreach ($product->find('.product-list-image-slider-item')[0]->find('img') as $img) {
                        echo $img->getAttribute('src');
                    }
                    dd($product->find('.product-item-photo')[0]->find('img'));
                    $mainPhoto = $product->find('.product-item-photo')[0]->find('img')[0]->getAttribute('src');
                }*/

                if ($productLink) {
                    $productPage = new Document($productLink, true);
                    if ($productPage) {
                        $productsData[$num]['name'] = @$productPage->find('.product-name')[0] ?
                        $productPage->find('.product-name')[0]->text() : '';
                        $elLink = array_filter(explode('/', $productsData[$num]['name']));
                        $productsData[$num]['sku'] = trim($elLink[count($elLink) - 1]);
                        

                        /*$productsData[$num]['description'] = @$productPage->find('.additional-attributes-wrapper')[0] ?
                            $productPage->find('.additional-attributes-wrapper')[0]->text() : '';*/
                            //dd(@$productPage->find('table[id=product-attribute-specs-table]')[0]->find('tr'));
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
                                // dd($row->find('td'));
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
                        $productsData[$num]['description'] = $description;

                        if (@$productPage->find('.product-gallery-carousel')[0]) {
                            foreach ($productPage->find('.product-gallery-carousel')[0]->find('img') as $image) {
                                $productsData[$num]['photo'][] = $image->getAttribute('src');
                            }
                        }
                        // set main photo
                        $productsData[$num]['main_photo'] = $productsData[$num]['photo'][0];

                        $options = array();
                        //dd($productPage->find('form[id=product_addtocart_form]')[0]->find('select'));

                        $script = $productPage->find('script[type=text/x-magento-init]');
                        $finalOptions = array();
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
                        // dd($tmpOptions);
                        $productsData[$num]['options'] = $tmpOptions;
                        $productsData[$num]['price'] = @$productPage->find('.price')[0] ?
                            str_replace('€', '', $productPage->find('.price')[0]->text()) : 0;;
                        $productsData[$num]['categories'] = '';
                        $productsData[$num]['short_descr'] = '';
                        $productsData[$num]['add_description'] = '';
                        $productsData[$num]['meta_description'] = @$productPage->find('meta[name=description]')[0] ?
                            $productPage->find('meta[name=description]')[0]->getAttribute('content') : '';
                        $productsData[$num]['meta_title'] = @$productPage->find('meta[name=title]')[0] ?
                            $productPage->find('meta[name=title]')[0]->getAttribute('content') : '';
                        $num++;
//dd($productsData);
                    }
                }
            }
        }

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
