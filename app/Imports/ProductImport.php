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

class ProductImport implements ToCollection
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
        $separator = '/';

        foreach ($rows as $key => $row) {

            if ($key >= 0) {
                if (!$row[0]) {
                    continue;
                }

                $product = new Product();
                $product->name  = $row[0];

                $product->sku = trim(str_replace(':', '', trim(str_replace('Code', '', $row[1]))));
                $product->slug = Str::slug($row[0]);
                /*if ($row[7]) {
                    $category = \App\Category::where('name', 'like', '%' .$row[7]. '%')->first();
                    $product->category_id = $category->id;
                }*/
                $product->category_id = 1;
                $product->brand_id = 53;

                $description = str_replace(array("\n", "\r"), '<br/>', @$row[2]);
                if(json_decode(@$row[6])) {
                    $description .= str_replace(array("\n", "\r"), '<br/>', implode(' ', json_decode($row[6])));
                } else {
                    $description .= str_replace(array("\n", "\r"), '<br/>', $row[6]);
                }
                $description .= str_replace(array("\n", "\r"), '<br/>', @$row[7]);
                $product->description = $description;

                
                $tmpOption = array();
                if ($row[5]) {
                    $options = json_decode($row[5]);

                    if (@count($options)) {
                        foreach($options as $t => $option) {
                            foreach ($option as $key => $_option) {
                                if ($key == 'label') {
                                    $groupName = 'caracole-'.Str::slug($_option);
                                    $optionGroup = \App\OptionGroup::where('code', '=', $groupName)->first();
                                    if (!$optionGroup) {
                                        $optionGroup = new OptionGroup();
                                        $optionGroup->name = ucfirst($_option);
                                        $optionGroup->code = $groupName;
                                        $optionGroup->save();
                                    }
                                    $tmpOption[$t]['group_id'] = $optionGroup->id;
                                    $tmpOption[$t]['label'] = $optionGroup->name;
                                }
                                if ($key == 'option') {
                                    foreach ($_option as $val) {
                                        $optionCode = Str::slug($val->name);
                                        $optionTbl = \App\Option::where('code', '=', $optionCode)
                                            ->where('group_id', $optionGroup->id)->first(); 
                                        if (!$optionTbl) {
                                            $optionTbl = new Option();  
                                            $optionTbl->group_id = $optionGroup->id;
                                            $optionTbl->name = ucfirst($val->name);
                                            $optionTbl->code = $optionCode;
                                            if ($val->thumb) {
                                                $pathinfo = pathinfo($val->thumb);
                                                $ext = $pathinfo['extension'];
                                                $ext = 'jpg';
        
                                                $thumbName = $pathinfo['filename'].'.'.$ext;
                                                $uploadedImage = $storagePathOpth.'June2020/'.$thumbName;
        
                                                $isCopied = @copy($val->thumb, $uploadedImage);
                                                $optionTbl->image = 'options/June2020/'.$thumbName;
                                                if ($isCopied) {
                                                    $_image = InterventionImage::make($uploadedImage)
                                                        ->orientate()
                                                        ->fit(50, 50)
                                                        ->encode($ext, 100);                                        
                                                    Storage::disk(config('voyager.storage.disk'))->put(
                                                        'options/June2020/'.str_replace('.'.$ext, '-cropped.'.$ext, $thumbName),
                                                        (string) $_image,
                                                        'public'
                                                    );
                                                }                                                                        
                                            } 
                                            $optionTbl->save();                                       
                                        }
                                        $tmpOption[$t]['selected'][] = $optionTbl->id;
                                    }
                                }
                            }
                        }
                    }
                }
                if (count($tmpOption)) {
                    $product->options = json_encode($tmpOption);
                }

                $product->meta_title = $row[11];
                $product->meta_description = $row[10];
                $product->price = 0;
                $product->active = 1;
                $product->shipping_opption_id = 2;
                $product->save();

                if (!file_exists($storagePath.$product->id)) {
                    Storage::disk('public')->makeDirectory('products/'.$product->id);
                }

                $arrPhotos = array();

                $images = json_decode($row[3]);

                $mainPhoto = $images[0];
                foreach ($images as $image) {
                    $url = $image;
                    $pathinfo = pathinfo($image);
                    
                    $ext = @$pathinfo['extension'];
                    //$ext = 'jpg';
                    $name = @$pathinfo['filename'].'.'.$ext;
                    $uploadedImage = $storagePath.$product->id.'/'.$name;
                    $isCopied = @copy($image, $uploadedImage);
                    if ($isCopied) {
                        // Image::make($uploadedImage)->resize(180, 240)->save($storagePath.$product->id.'/180X240/'.$name);
                        $_image = InterventionImage::make($image)
                            ->orientate()
                            ->fit(380, 340)
                            ->encode($ext, 100);
                        Storage::disk(config('voyager.storage.disk'))->put(
                            'products'.$separator.$product->id.$separator.str_replace('.'.$ext, '-cropped.'.$ext, $name),
                            (string) $_image,
                            'public'
                        );
                        //Image::make($uploadedImage)->crop(180, 240, 0, 0)->save($storagePath.$product->id.'/'.str_replace('.'.$ext, '_cropped.'.$ext, $name));
                        $arrPhotos[] = 'products/'.$product->id.'/'.$name;
                    }
                }
                $product->image = json_encode($arrPhotos, JSON_UNESCAPED_UNICODE);

                if ($row[4] || $mainPhoto) {
                    $image = $row[4] ? $row[4] : $mainPhoto;
                    $url = $image;
                    $pathinfo = pathinfo($image);
                    
                    $ext = @$pathinfo['extension'];
                    // $ext = 'jpg';
                    $name = @$pathinfo['filename'].'.'.$ext;

                    $uploadedImage = $storagePath.$product->id.'/'.$name;
                    $isCopied = @copy($image, $uploadedImage);
                    if ($isCopied) {
                        // Image::make($uploadedImage)->resize(180, 240)->save($storagePath.$product->id.'/180X240/'.$name);
                        $croppedFileName = str_replace('.'.$ext, '-cropped.'.$ext, $name);

                        $cropped = InterventionImage::make($uploadedImage);
                        $cropped->resize(300, 300);
                        $cropped->save($storagePath.$product->id.$separator.$croppedFileName); 
                            //->fit(380, 340)
                            //->encode($ext, 100);

                            /*Storage::disk(config('voyager.storage.disk'))->put(
                            'products'.$separator.$product->id.$separator.str_replace('.'.$ext, '-cropped.'.$ext, $name),
                            (string) $_image,
                            'public'
                        );*/
                        //Image::make($uploadedImage)->crop(180, 240, 0, 0)->save($storagePath.$product->id.'/'.str_replace('.'.$ext, '_cropped.'.$ext, $name));
                        //$arrPhotos[] = 'products/'.$product->id.'/'.$name;
                    }
                    $product->main_photo = 'products'.$separator.$product->id.$separator.$name;
                }
                
                $product->save();
            }
        }
    }



}


