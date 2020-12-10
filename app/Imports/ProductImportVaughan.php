<?php

namespace App\Imports;

use App\Category;
use App\Product;
use App\OptionGroup;
use App\Option;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;

//use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use phpDocumentor\Reflection\Types\Array_;
use Intervention\Image\Facades\Image as InterventionImage;

class ProductImportVaughan implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        $storagePath = str_replace('\\', '/', storage_path()).'/app/public_html/products/';
        $storagePathOpth = str_replace('\\', '/', storage_path()).'/app/public_html/options/';
//dd($rows);
        foreach ($rows as $key => $row) {
            $images = (json_decode($row[4]));
            if ($key >= 0) {
 
                $product = new Product();
                $product->name  = $row[0];
                $product->slug = Str::slug($row[0]);
                $product->sku = trim(str_replace(':', '', trim(str_replace('Code', '', $row[1]))));
                $product->category_id = 8;
                $product->brand_id = 58;

                $product->description = $row[2];

                $options = json_decode($row[4]);
                // cushion group id for color 429
                $groupId[] = 432;
                $groupId[] = 435;
                $tmpOption = array();
                foreach($options as $key => $option) {
                    $optionGroup = \App\OptionGroup::where('id', $groupId[$key])->first();
                    
                    foreach ($option as $_option) {
                        $tmpOption[$key]['group_id'] = $groupId[$key];
                        $tmpOption[$key]['label'] = $optionGroup->name;
                        // add color
                        $optName = count((array)$_option) > 1 ? $_option[1] : $_option;
                        $tblOption = \App\Option::where('name', '=', $optName)->where('group_id', $groupId[$key])->first();
                        //dd($tblOption);
                        if (!$tblOption) {
                            $tblOption = new Option();
                            $tblOption->group_id = $groupId[$key];
                            $tblOption->name = $optName;
                            $tblOption->code = Str::slug($optName);
                            $tblOption->save();
                        } 
                        $optId = $tblOption->id;
                        $tmpOption[$key]['selected'][] = $optId;
                    }
                }
                // dd($tmpOption);
                $product->options = json_encode($tmpOption);
                
                $product->meta_title = $row[10];
                $product->meta_description = $row[9];
                $product->price = NULL;
                $product->active = 1;
                $product->shipping_opption_id = 3;
                $product->save();

                if (!file_exists($storagePath.$product->id)) {
                    Storage::disk('public')->makeDirectory('products/'.$product->id);
                }

                $arrPhotos = array();
                $images = json_decode($row[3]);
   
                foreach ($images as $image) {
                    $url = $image;
                    $name = substr($image, strrpos($image, '/') + 1);
                    $uploadedImage = $storagePath.$product->id.'/'.$name;
                    $ext = pathinfo($image, PATHINFO_EXTENSION);
                    $isCopied = @copy($image, $uploadedImage);
                    if ($isCopied) {
                        // Image::make($uploadedImage)->resize(180, 240)->save($storagePath.$product->id.'/180X240/'.$name);
                        $_image = InterventionImage::make($image)
                            ->orientate()
                            ->fit(180, 240)
                            ->encode($ext, 100);
                        Storage::disk(config('voyager.storage.disk'))->put(
                            'products'.DIRECTORY_SEPARATOR.$product->id.DIRECTORY_SEPARATOR.str_replace('.'.$ext, '-cropped.'.$ext, $name),
                            (string) $_image,
                            'public'
                        );
                        //Image::make($uploadedImage)->crop(180, 240, 0, 0)->save($storagePath.$product->id.'/'.str_replace('.'.$ext, '_cropped.'.$ext, $name));
                        $arrPhotos[] = 'products/'.$product->id.'/'.$name;
                    }
                }
                $product->image = json_encode($arrPhotos, JSON_UNESCAPED_UNICODE);
                $product->save();
            }
        }
        //exit;
    }



}
