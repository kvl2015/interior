<?php
namespace App\Exports;

// use App\Invoice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use DiDom\Document;

class ProductExportArtemide implements FromCollection
{
    public function collection()
    {
        ini_set('max_execution_time', 270000);
        $num = 0;
        //dd('dddd');
        $productsData = array();
        for ($i = 1; $i<=1; $i++) {
            $link = 'https://www.baccarat.com/en/symphony-of-lights/accessories-lights/';
            //echo $link.'<br/>';
            //exit;
            $document = new Document($link, true);
            dd($document);
            $productGrids = $document->find('.grid__item');

            foreach($productGrids as $key => $product) {
                // parse product info
                $productLink = $product->find('.grid__image')[0]->getAttribute('href');
                dd($productLink);

                $productPage = new Document($productLink, true);

                // get full information about product
                $productsData[$num]['name'] = @$productPage->find('.product-details__title')[0] ?
                    $productPage->find('.product-details__title')[0]->text() : '';
                $productsData[$num]['code'] = @$productPage->find('.product-details__code')[0] ?
                    $productPage->find('.product-details__code')[0]->text() : '';
                $description = '';
                // product-details__text
                if ($productPage->find('.product-details__text')[0]) {
                    foreach ($productPage->find('.product-details__text') as $info) {
                        foreach ($info->find('p') as $p) {
                            $description .= '<p>' .$p->text(). '</p>';
                        }
                    }
                }
                // product-details__table
                if (@$productPage->find('.product-details__table')[0]) {
                    $description .= '<table>';
                    foreach (@$productPage->find('.product-details__table')[0]->find('tr') as $row) {
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

                if (count($productPage->find('.product-details__photo'))) {
                    foreach ($productPage->find('.product-details__photo') as $img) {
                        $productsData[$num]['photo'][] = $img->find('img')[0]->getAttribute('src');
                    }
                }

                //check for select
                $options = $productPage->find('.product-details__option');
                //dd($options);
                foreach ($options as $m => $dataOption) {
                    $_options = $dataOption->find('option');
                    if (count($_options)) {
                        foreach ($_options as $option) {
                            $optionCode = array_filter(explode('/', $option->getAttribute('value')));
                            //dd($optionCode[(count($optionCode))]);
                            $productsData[$num]['options'][$m][] = array($optionCode[(count($optionCode))], $option->text());
                            //$productsData[$num]['options'][$m][]['code'] = $option->getAttribute('value');
                        }
                    } else {
                        $textOption = $productPage->find('.product-details__option')[0]->text();
                        $productsData[$num]['options'][$m][] = trim($textOption);
                    }
                }
                $productsData[$num]['price'] = '';
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
