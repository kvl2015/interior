<?php
namespace App\Exports;

// use App\Invoice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use DiDom\Document;

/*
 * File export structure
 *
 * $productsData[$num]['name']
 * $productsData[$num]['code']
 * $productsData[$num]['description']
 * $productsData[$num]['photo']
 * $productsData[$num]['option']
 * $productsData[$num]['price']
 * $productsData[$num]['categories']
 * $productsData[$num]['short_description']
 * $productsData[$num]['add_description']
 * $productsData[$num]['meta_description']
 * $productsData[$num]['meta_title']
 *
 *
 */
class ProductExportCorbinbronze implements FromCollection
{
    public function collection()
    {
        ini_set('max_execution_time', 270000);
        $num = 0;
        $productsData = array();
        for ($i = 1; $i<=1; $i++) {
            $link = 'https://corbinbronze.com/portfolio/paintings/';
            //echo $link.'<br/>';
            $document = new Document($link, true);

            $productGrids = $document->find('div[id=projectGrid]')[0]->find('.project');

            foreach($productGrids as $key => $product) {
                // parse product info
                $productLink = $product->find('a')[0]->getAttribute('href');
                $elLink = array_filter(explode('/', $productLink));
                $productPage = new Document($productLink, true);

                // get full information about product
                $productsData[$num]['name'] = @$productPage->find('.infinite-page-title')[0] ?
                    $productPage->find('.infinite-page-title')[0]->text() : '';
                $productsData[$num]['code'] = $elLink[count($elLink)];
                $productsData[$num]['description'] = @$productPage->find('.inside')[0]->find('p')[0] ?
                    $productPage->find('.inside')[0]->find('p')[0]->text() : '';
                //if (count($productPage->find('.product-details__photo'))) {
                    foreach ($productPage->find('.right')[0]->find('img') as $img) {
                        $productsData[$num]['photo'][] = $img->getAttribute('src');
                    }

                //}

                $options = array();
                /*foreach ($options as $m => $dataOption) {
                    $_options = $dataOption->find('option');
                    if (count($_options)) {
                        foreach ($_options as $option) {
                            $optionCode = array_filter(explode('/', $option->getAttribute('value')));
                            $productsData[$num]['options'][$m][] = array($option->text(), $optionCode[(count($optionCode))]);
                        }
                    } else {
                        $textOption = $productPage->find('.product-details__option')[0]->text();
                        $productsData[$num]['options'][$m][] = trim($textOption);
                    }
                }*/
                $productsData[$num]['options'] = '';
                $productsData[$num]['price'] = '';
                $productsData[$num]['categories'] = '';
                $productsData[$num]['short_descr'] = '';
                $productsData[$num]['add_description'] = '';


                $productsData[$num]['meta_description'] = @$productPage->find('meta[name=description]')[0] ?
                    $productPage->find('meta[name=description]')[0]->getAttribute('content') : '';
                $productsData[$num]['meta_title'] = @$productPage->find('title')[0] ?
                    $productPage->find('title')[0]->text() : '';

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
