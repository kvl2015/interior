<?php
namespace App\Exports;

// use App\Invoice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use DiDom\Document;

class ProductExportBeaumontandfletcher implements FromCollection
{
    public function collection()
    {
        ini_set('max_execution_time', 270000);
        $num = 0;
        $productsData = array();
        for ($i = 1; $i<=1; $i++) {
            $link = 'https://www.beaumontandfletcher.com/accessories/couture-cushions/';
            //echo $link.'<br/>';
            $document = new Document($link, true);
            
            $productGrids = $document->find('.product-listing-block');
            //$name = $productGrids[0]->text();
            //$name = preg_replace("/[^a-z-&]/i", "", $name);
            foreach ($productGrids as $product) {
                $productLink = $product->find('a')[0]->getAttribute('href');
                $productPage = new Document($productLink, true);

                $productsData[$num]['name'] = trim(preg_replace('/\s\s+/', ' ', $productPage->find('h1')[0]->text()));
//dd($productsData);
                $productsData[$num]['sku'] = '';
                $description = '';
                if ($productPage->find('.small-text-content-holder')[0]) {
                    foreach ($productPage->find('.small-text-content-holder') as $info) {
                        foreach ($info->find('p') as $p) {
                            $description .= '<p>' .$p->text(). '</p>';
                        }
                    }
                }
                if ($productPage->find('.small-text-addition-holder')[0]) {
                    foreach ($productPage->find('.small-text-addition-holder') as $info) {
                        foreach ($info->find('p') as $p) {
                            $description .= '<p>' .$p->text(). '</p>';
                        }
                    }
                }
                // get product details imperial
                if (@$productPage->find('table.imperial-table')[0]) {
                    $description .= '<table>';
                    // heade table
                    foreach ($productPage->find('table.imperial-table')[0]->find('thead')[0]->find('tr') as $row) {
                        $description .= '<tr>';
                        foreach ($row->find('th') as $th) {
                            $description .= '<th>'.trim(preg_replace('/\s\s+/', ' ', $th->text())).'</td>';
                        }
                        $description .= '</tr>';
                    }
                    // body table
                    foreach ($productPage->find('table.imperial-table')[0]->find('tbody')[0]->find('tr') as $row) {
                        $description .= '<tr>';
                        foreach ($row->find('td') as $td) {
                            $description .= '<th>'.trim(preg_replace('/\s\s+/', ' ', $td->text())).'</td>';
                        }
                        $description .= '</tr>';
                    }
                    $description .= '</table>';
                }
                // get product details metric
                if (@$productPage->find('table.metric-table')[0]) {
                    $description .= '<table>';
                    // heade table
                    foreach ($productPage->find('table.metric-table')[0]->find('thead')[0]->find('tr') as $row) {
                        $description .= '<tr>';
                        foreach ($row->find('th') as $th) {
                            $description .= '<th>'.trim(preg_replace('/\s\s+/', ' ', $th->text())).'</td>';
                        }
                        $description .= '</tr>';
                    }
                    // body table
                    foreach ($productPage->find('table.metric-table')[0]->find('tbody')[0]->find('tr') as $row) {
                        $description .= '<tr>';
                        foreach ($row->find('td') as $td) {
                            $description .= '<th>'.trim(preg_replace('/\s\s+/', ' ', $td->text())).'</td>';
                        }
                        $description .= '</tr>';
                    }
                    $description .= '</table>';
                }                
                $productsData[$num]['description'] = $description;
                
//dd($description);

                if ($productPage->find('.large-image-gal-container')) {
                    //echo "sss";exit;
                    foreach ($productPage->find('.large-image-gal-container')[0]->find('img') as $img) {
                        $productsData[$num]['photo'][] = $img->getAttribute('src');
                    }
                }

                //get options by product-variants-item
                $tmpOptions = array();
                /*foreach ($productPage->find('.product-variants-item') as $key => $option) {
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
                */
                $productsData[$num]['options'] = $tmpOptions;
                $num++;
                //dd($productsData);
               
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
