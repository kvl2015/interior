<?php
namespace App\Exports;

// use App\Invoice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use DiDom\Document;

class ProductExportCaracole implements FromCollection
{
    public function collection()
    {
        ini_set('max_execution_time', 270000);
        $num = 0;

// $arrLinks[] = array('Bedroom', 'https://caracole.com/Product/Category/BEDROOM%20-%20ARMOIRES', 1);
// $arrLinks[] = array('Bedroom', 'https://caracole.com/Product/Category/BEDROOM%20-%20BEDS', 1);

        $productsData = array();
        for ($i = 1; $i<=23; $i++) {
            $link = 'https://caracole.com/Product/Gallery/1?page='.$i;
            //echo $link.'<br/>';
            $document = new Document($link, true);
            
            $productGrids = $document->find('shopping-gallery')[0];
//dd($productGrids);
            foreach ($productGrids->find('gallery-item') as $product) {
                $productLink = 'https://caracole.com'.$product->find('a')[0]->getAttribute('href');
                $sku = trim(preg_replace('/\s\s+/', ' ', $product->find('.sku')[0]->text()));
                $name = trim(preg_replace('/\s\s+/', ' ', $product->find('h6')[0]->text()));
                $mainPhoto = $product->find('img')[0]->getAttribute('src');

//echo $productLink;exit;
                $productPage = new Document($productLink, true);

                //$productsData[$num]['name'] = trim(preg_replace('/\s\s+/', ' ', $productPage->find('h5')[0]->text()));
                $productsData[$num]['name'] = $name;
                $productsData[$num]['sku'] = $sku;
//dd($productsData);
                $description = '';
                if (@$productPage->find('.shopping-item-detail')[0]) {
                    foreach ($productPage->find('.shopping-item-detail')[0]->find('div') as $info) {
                        //foreach ($info->find('p') as $p) {
                            $description .= '<p>' .trim(preg_replace('/\s\s+/', ' ', $info->text())). '</p>';
                        //}
                    }
                }
                $productsData[$num]['description'] = $description;
                
// echo ($description);exit;
      
                if ($productPage->find('alt-images')) {
                    if (count($productPage->find('alt-images')[0]->find('alt-image'))) {
                        foreach ($productPage->find('alt-images')[0]->find('alt-image') as $img) {

                            $productsData[$num]['photo'][] = $img->getAttribute('data-zoom-src');
                        }
//dd($productsData[$num]['photo']);
                    } else {
                        //dd($productPage->find('magic-zoom')[0]->getAttribute('src'));
                        if (@$productPage->find('magic-zoom')[0]) {
                            $productsData[$num]['photo'][] = $productPage->find('magic-zoom')[0]->getAttribute('src');
                        } else {
                            $productsData[$num]['photo'] = array();
                        }
                    }
                } else {

                    if (@$productPage->find('.mz-figure')[0]) {
                        $productsData[$num]['photo'][] = $productPage->find('.mz-figure')[0]->find('img')[0]->getAttribute('src');
                    } else {
                        $productsData[$num]['photo'] = array();
                    }
                }
                $productsData[$num]['main_photo'] = $mainPhoto;
//dd($productsData);
                //get options by product-variants-item
                $tmpOptions = array();
                foreach ($productPage->find('.finish-box') as $key => $option) {
                    if ($option) {
                        $tmpOptions[$key]['label'] = $option->getAttribute('ea-bind');
                        if ($option->find('.finish-item')){
                            foreach ($option->find('.finish-item') as $t => $li) {
                                $thumb = $li->find('img')[0]->getAttribute('src');
                                $name = trim(preg_replace('/\s\s+/', ' ', $li->find('span')[0]->text()));
                                if (!$name) $name = 'option-'.$t;
                                $tmpOptions[$key]['option'][] = array(
                                    'thumb' => $thumb,
                                    'name' => $name
                                );
                                
                            }
                        } 
                    }
                }
//dd($tmpOptions);
                $productsData[$num]['options'] = $this->super_unique($tmpOptions);
                $productsData[$num]['price'] = 0;
                $productsData[$num]['category'] = trim(preg_replace('/\s\s+/', ' ', $productPage->find('h5')[0]->text()));
                $productsData[$num]['short_descr'] = '';
                $productsData[$num]['add_description'] = '';
                $productsData[$num]['meta_description'] = @$productPage->find('meta[name=description]')[0] ?
                    $productPage->find('meta[name=description]')[0]->getAttribute('content') : '';
                $productsData[$num]['meta_title'] = @$productPage->find('meta[name=title]')[0] ?
                $productPage->find('meta[name=title]')[0]->getAttribute('content') : '';                
                $productsData[$num]['link'] = $productLink;
                //if (count($productsData[$num]['options']['options']))
                $num++;
// dd($productsData);
            }
        }
// dd($productsData);

        return new Collection($productsData);
    }

    function super_unique($array) {
      $result = array_map("unserialize", array_unique(array_map("serialize", $array)));
    
      foreach ($result as $key => $value)
      {
        if ( is_array($value) )
        {
          $result[$key] = $this->super_unique($value);
        }
      }
    
      return $result;
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
