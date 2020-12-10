<?php
namespace App\Exports;

// use App\Invoice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use DiDom\Document;

class ProductExportAdrianahoyos implements FromCollection
{
    public function collection()
    {
        ini_set('max_execution_time', 270000);
        $num = 0;
        $productsData = array();
        for ($i = 1; $i<=1; $i++) {
            $link = 'https://www.adrianahoyos.com/ni/en/occasional-pieces-15';
            //echo $link.'<br/>';
            $document = new Document($link, true);
            $productGrids = $document->find('.product-miniature');
            $name = $productGrids[0]->text();
            $name = preg_replace("/[^a-z-&]/i", "", $name);
            foreach ($productGrids as $grid) {
                $categoryLink = $grid->find('a')[0]->getAttribute('href');
                $productDocument = new Document($categoryLink, true);
                $productsGrid = $productDocument->find('.dor-thumbnail-container');
                foreach ($productsGrid as $key => $product) {
                    $productLink = $product->find('.product-title')[0]->find('a')[0]->getAttribute('href');
                    //$productLink = 'https://www.adrianahoyos.com/ni/en/inicio/660-12330-grafito-loveseat-200.html#/294-legs-r';
                    $productPage = new Document($productLink, true);

                    $productsData[$num]['name'] = $productPage->find('.titulos')[0]->text();
                    $productsData[$num]['sku'] = trim(preg_replace('/\s\s+/', ' ', $productPage->find('.tt-productcode')[0]->text()));
                    $description = '';
                    if ($productPage->find('.product-description')[0]) {
                        foreach ($productPage->find('.product-description') as $info) {
                            foreach ($info->find('p') as $p) {
                                $description .= '<p>' .$p->text(). '</p>';
                            }
                        }
                    }
                    // get product details
                    if (@$productPage->find('.product-measures')[0]->find('table')[0]) {
                        $description .= '<table>';
                        foreach ($productPage->find('.product-measures')[0]->find('table')[0]->find('tr') as $row) {
                            $description .= '<tr>';
                            // dd($row->find('td'));
                            $description .= '<td>'.trim(preg_replace('/\s\s+/', ' ', $row->find('td')[0]->text())).'</td>';
                            $description .= '<td>'.trim(preg_replace('/\s\s+/', ' ', $row->find('td')[1]->text())).'</td>';
                            
                            $description .= '</tr>';
                        }
                        $description .= '</table>';
                    }
                    $productsData[$num]['description'] = $description;

                    if (count($productPage->find('.MagicToolboxContainer'))) {
                        foreach ($productPage->find('.MagicToolboxContainer')[0]->find('a') as $img) {
                            $productsData[$num]['photo'][] = $img->getAttribute('href');
                        }
                    }

                    //get options by product-variants-item
                    $tmpOptions = array();
                    foreach ($productPage->find('.product-variants-item') as $key => $option) {
                        if ($option) {
                            if(!$option->find('p')) {
                                if ($option->find('.open'))
                                    $tmpOptions[$key]['label'] = @trim(preg_replace('/\s\s+/', ' ', $option->find('.open')[0]->text()));
                                else
                                $tmpOptions[$key]['label'] = '';
                            } else {
                                $tmpOptions[$key]['label'] = trim(preg_replace('/\s\s+/', ' ', $option->find('p')[0]->text()));
                            }
                            //$tmpOptions[$key]['label'] = trim(preg_replace('/\s\s+/', ' ', $option->find('p')[0]->text()));
                            foreach ($option->find('li') as $t => $li) {
                                $thumb = (str_replace('background-image: url', 'https://www.adrianahoyos.com/', str_replace(array('(', ')'), '', $li->find('.texture')[0]->getAttribute('style'))));
                                $name = trim(preg_replace('/\s\s+/', ' ', $li->find('.cf-m-info-cat-name')[0]->text()));
                                if (!$name) $name = 'option-'.$t;
                                $tmpOptions[$key]['option'][] = array(
                                    'thumb' => $thumb,
                                    'name' => $name
                                );
                            }
                            }
                    }
                    //dd($tmpOptions);
                    $productsData[$num]['options'] = $tmpOptions;
                    $num++;
                    //dd($productsData);
                }
                //dd($categoryLink);
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
