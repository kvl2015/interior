<?php
namespace App\Exports;

// use App\Invoice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use DiDom\Document;

class ProductExportBocadolobo implements FromCollection
{
    public function collection()
    {
        ini_set('max_execution_time', 270000);
        $num = 0;
        $productsData = array();
        for ($i = 1; $i<=1; $i++) {
            $link = 'https://www.bocadolobo.com/en/products/';
            //echo $link.'<br/>';
            $document = new Document($link, true);
            
            $productGrids = $document->find('div[id=ap-page-beggining]')[0]->find('.col-sm-4');
            //$name = $productGrids[0]->text();
            //$name = preg_replace("/[^a-z-&]/i", "", $name);
            foreach ($productGrids as $product) {
                $productLink = 'https://www.bocadolobo.com'.$product->find('a')[0]->getAttribute('href');
//echo $productLink;exit;
                $productPage = new Document($productLink, true);

                $productsData[$num]['name'] = trim(preg_replace('/\s\s+/', ' ', $productPage->find('h1')[0]->text())).' '.
                trim(preg_replace('/\s\s+/', ' ', $productPage->find('h2')[0]->text()));
//dd($productsData);
                $productsData[$num]['sku'] = '';
                $description = '';
                if (@$productPage->find('div[id=adicional_information]')[0]) {
                    foreach ($productPage->find('div[id=adicional_information]') as $info) {
                        foreach ($info->find('p') as $p) {
                            $description .= '<p>' .trim(preg_replace('/\s\s+/', ' ', $p->text())). '</p>';
                        }
                    }
                }
                $productsData[$num]['description'] = $description;
                
//echo ($description);exit;

                if ($productPage->find('.slide-image')) {
                    //echo "sss";exit;
                    foreach ($productPage->find('.slide-image') as $img) {
                        $productsData[$num]['photo'][] = 'https://www.bocadolobo.com'.$img->find('a')[0]->getAttribute('href');
                    }
                }
//dd($productsData);
                //get options by product-variants-item
                $tmpOptions = array();
                foreach ($productPage->find('.product_info')[0]->find('.finishes_product') as $key => $option) {
                    if ($option) {
                        $tmpOptions[$key]['label'] = 'Finish';
                        if ($option->find('a')){
                            foreach ($option->find('a') as $t => $li) {
                                $thumb = 'https://www.bocadolobo.com'.$li->find('img')[0]->getAttribute('srcset');
                                $name = trim(preg_replace('/\s\s+/', ' ', $li->find('img')[0]->getAttribute('alt')));
                                if (!$name) $name = 'option-'.$t;
                                $tmpOptions[$key]['option'][] = array(
                                    'thumb' => $thumb,
                                    'name' => $name
                                );
                                if ($li->getAttribute('href') != $productLink) {
                                    $addLink = 'https://www.bocadolobo.com'.str_replace('https://www.bocadolobo.com', '', $li->getAttribute('href'));
                                    if ($addLink != $productLink) {
                                        $productPageAdd = new Document($addLink, true);
                                        if ($productPageAdd->find('.slide-image')) {
                                            foreach ($productPageAdd->find('.slide-image') as $img) {
                                                $productsData[$num]['photo'][] = 'https://www.bocadolobo.com'.$img->find('a')[0]->getAttribute('href');
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            foreach ($option->find('img') as $t => $li) {
                                $thumb = 'https://www.bocadolobo.com'.$li->find('img')[0]->getAttribute('srcset');
                                $name = trim(preg_replace('/\s\s+/', ' ', $li->find('img')[0]->getAttribute('alt')));
                                if (!$name) $name = 'option-'.$t;
                                $tmpOptions[$key]['option'][] = array(
                                    'thumb' => $thumb,
                                    'name' => $name
                                );
                                
                            }
                        }
                        
                    }
                }
                
                $productsData[$num]['options'] = $this->super_unique($tmpOptions);
                $productsData[$num]['link'] = $productLink;
                //if (count($productsData[$num]['options']['options']))
                $num++;
//dd($productsData);
            }
        }
//dd($productsData);

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
