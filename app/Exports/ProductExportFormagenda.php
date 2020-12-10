<?php
namespace App\Exports;

// use App\Invoice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use DiDom\Document;

class ProductExportFormagenda implements FromCollection
{
    public function collection()
    {
        ini_set('max_execution_time', 270000);
        $num = 0;
        $productsData = array();
        for ($i = 1; $i<=1; $i++) {
            $link = 'https://www.formagenda.com/en/product-category/accessoires/';
            //echo $link.'<br/>';
            $document = new Document($link, true);

            $productGrids = $document->find('.nm-products')[0]->find('li');
//dd($productGrids);
            foreach($productGrids as $key => $product) {
                // parse product info
                $productLink = $product->find('a')[0]->getAttribute('href');
                $elLink = array_filter(explode('/', $productLink));
dd($productLink);
                $productPage = new Document($productLink, true);

                // get full information about product
                $productsData[$num]['name'] = @$productPage->find('.entry-title')[0] ?
                    $productPage->find('.entry-title')[0]->text() : '';
                $productsData[$num]['code'] = $elLink[count($elLink)];
                $productsData[$num]['description'] = @$productPage->find('.woocommerce-product-details__short-description')[0] ?
                    $productPage->find('.woocommerce-product-details__short-description')[0]->text() : '';
                if (count($productPage->find('.product-details__photo'))) {
                    foreach ($productPage->find('.product-details__photo') as $img) {
                        $productsData[$num]['photo'][] = $img->find('img')[0]->getAttribute('src');
                    }
                }
                /*$productsData[$num]['photo'] = @$productPage->find('.product-details__photo')[0] ?
                    $productPage->find('.product-details__photo')[0]->find('img')[0]->getAttribute('src') : '';*/

                //check for select
                //dd($productPage->find('.product-details__option')[0]->find('option'));
                $options = $productPage->find('.product-details__option');
                //dd($options);
                foreach ($options as $m => $dataOption) {
                    $_options = $dataOption->find('option');
                    if (count($_options)) {
                        foreach ($_options as $option) {
                            $optionCode = array_filter(explode('/', $option->getAttribute('value')));
                            //dd($optionCode[(count($optionCode))]);
                            $productsData[$num]['options'][$m][] = array($option->text(), $optionCode[(count($optionCode))]);
                            //$productsData[$num]['options'][$m][]['code'] = $option->getAttribute('value');
                        }
                    } else {
                        $textOption = $productPage->find('.product-details__option')[0]->text();
                        $productsData[$num]['options'][$m][] = trim($textOption);
                    }
                }
                $productsData[$num]['price'] = '';
                $productsData[$num]['categories'] = '';
                if (count($productPage->find('.product-details__text'))) {
                    foreach ($productPage->find('.product-details__text') as $text) {
                        $productsData[$num]['short_descr'][] = $text->text();
                    }
                }
                $productsData[$num]['add_description'] = $productPage->find('.product-details__table')[0]->text();

                $productsData[$num]['meta_description'] = @$productPage->find('meta[name=description]')[0] ?
                    $productPage->find('meta[name=description]')[0]->getAttribute('content') : '';
                $productsData[$num]['meta_title'] = @$productPage->find('title')[0] ?
                    $productPage->find('title')[0]->text() : '';
                $productsData[$num]['robots'] = @$productPage->find('meta[name=robots]')[0] ?
                    $productPage->find('meta[name=robots]')[0]->getAttribute('content') : '';

                $num++;
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
