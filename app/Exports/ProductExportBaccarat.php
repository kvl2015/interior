<?php
namespace App\Exports;

// use App\Invoice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use DiDom\Document;

class ProductExportBaccarat implements FromCollection
{
    public function collection()
    {
        ini_set('max_execution_time', 270000);
        $num = 0;
        $productsData = array();
        for ($i = 1; $i<=1; $i++) {
            $link = 'https://www.baccarat.com/en/symphony-of-lights/ceiling/chandeliers/';
            //echo $link.'<br/>';
            //exit;
            $document = new Document($link, true);
            $productGrids = $document->find('.product-tile-container');

            foreach($productGrids as $key => $product) {
                $price = $product->find('.product-sales-price')[0]->text();
                // dd($price);
                // parse product info
                $productLink = 'https://www.baccarat.com'.$product->find('a')[0]->getAttribute('href');

                $productPage = new Document($productLink, true);

                // get full information about product
                $productsData[$num]['name'] = @$productPage->find('.product-name')[0] ?
                    $productPage->find('.product-name')[0]->text() : '';
                $productsData[$num]['sku'] = '';
                $description = '';
                // product-details__text
                if ($productPage->find('.product-short-description')[0]) {
                    foreach ($productPage->find('.product-short-description') as $info) {
                        foreach ($info->find('p') as $p) {
                            $description .= '<p>' .$p->text(). '</p>';
                        }
                    }
                }
                if (@$productPage->find('.product-information-text')[0]) {
                    foreach ($productPage->find('.product-information-text') as $info) {
                        foreach ($info->find('p') as $p) {
                            $description .= '<p>' .$p->text(). '</p>';
                        }
                    }
                }
                // .product-characteristic
                $table = '<table>';
                if (@$productPage->find('.product-characteristic')[0]) {
                    foreach ($productPage->find('.product-characteristic')[0]->find('.row') as $info) {
                        $table .=  '<tr>';
                        foreach ($info->find('.columns') as $p) {
                            //dd($p->text());
                            $table .= '<td>' .$p->text(). '</td>';
                        }
                        $table .= '</tr>';
                    }
                }
                $table .= '</table>';
                $productsData[$num]['description'] = $description.'<p>'.$table.'</p>';
                
                if (count($productPage->find('.product-image-container'))) {
                    foreach ($productPage->find('.product-image-container') as $img) {
                        $productsData[$num]['photo'][] = $img->find('img')[0]->getAttribute('src');
                    }
                }

                //check for select
                $options = @$productPage->find('.product-variations')[0];
                $tmpOptions = array();
                if ($options) {
                    foreach ($options->find('li') as $_key => $li) {
                        if ($li->find('ul')) {
                            $tmpOptions[$_key]['label'] = $li->find('ul')[0]->getAttribute('id');
                            //echo "ddddd";exit;
                            foreach ($li->find('ul')[0]->find('li') as $_li) {
                                $thumb = '';
                                if ($_li->find('a')[0]->find('img'))
                                    $thumb = $_li->find('a')[0]->find('img')[0]->getAttribute('src');
                                $tmpOptions[$_key]['option'][] = array(
                                    'name' => trim(preg_replace('/\s\s+/', ' ', $_li->find('a')[0]->text())),
                                    'thumb' => $thumb
                                );
                                //dd($_li->find('a')[0]->text());
                            }
                            // dd($tmpOptions);
                        } else {
                            if (@$li->find('button')[0]) {
                                $tmpOptions[$_key]['label'] = 'baccarat options';
                                $tmpOptions[$_key]['option'][] = array('name' => trim(preg_replace('/\s\s+/', ' ', @$li->find('button')[0]->text())));
                            }
                        }
                    }
                }
                $productsData[$num]['options'] = $tmpOptions;
                $productsData[$num]['price'] = $price;
                $productsData[$num]['categories'] = '';
                $productsData[$num]['add_description'] = '';

                $productsData[$num]['meta_description'] = @$productPage->find('meta[name=description]')[0] ?
                    $productPage->find('meta[name=description]')[0]->getAttribute('content') : '';
                $productsData[$num]['meta_title'] = @$productPage->find('title')[0] ?
                    $productPage->find('title')[0]->text() : '';
                $productsData[$num]['robots'] = @$productPage->find('meta[name=robots]')[0] ?
                    $productPage->find('meta[name=robots]')[0]->getAttribute('content') : '';

                $num++;
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
