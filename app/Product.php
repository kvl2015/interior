<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Models\Category;
use TCG\Voyager\Traits\Translatable;
use TCG\Voyager\Traits\Resizable;

class Product extends Model
{
    use Translatable,
        Resizable;

    protected $translatable = ['name', 'description', 'meta_title', 'meta_description'];

    public function categoryId(){
        return $this->belongsTo(Category::class);
    }

    public function category() {
        return $this->belongsTo('App\Category', 'category_id');
    }


    public function brandId(){
        return $this->belongsTo(Brand::class);
    }

    public function brand() {
        return $this->belongsTo('App\Brand', 'brand_id');
    }

    public function shippingOpptionId(){
        return $this->belongsTo(ShippingOption::class);
    }

    public function shipping() {
        return $this->belongsTo('App\ShippingOption', 'shipping_opption_id');
    }

    public function designerId(){
        return $this->belongsTo(Designer::class);
    }

    public function designer() {
        return $this->belongsTo('App\Designer', 'brand_id');
    }

    public function collectionId(){
        return $this->belongsTo(Collection::class);
    }

    public function collection() {
        return $this->belongsTo('App\Collection', 'collection_id');
    }

    public function roomId(){
        return $this->belongsTo(Room::class);
    }

    public function room() {
        return $this->belongsTo('App\Room', 'room_id');
    }
    
    public function styleId(){
        return $this->belongsTo(Style::class);
    }

    public function style() {
        return $this->belongsTo('App\Style', 'style_id');
    }     

}
