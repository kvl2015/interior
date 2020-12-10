<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use TCG\Voyager\Traits\Translatable;
use Illuminate\Support\Facades\Cache;
use TCG\Voyager\Traits\Resizable;




class Category extends Model
{
    use Translatable,
        Resizable;

    protected $translatable = ['name', 'description', 'meta_title', 'meta_description', 'seo_title', 'seo_content', 'product_meta_title', 'product_meta_description'];

    public $fillable = ['parent_id'];

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function childs() {
        return $this->hasMany('App\Category','parent_id','id')->orderBy('order') ;
    }

    public function parentId() {
        return $this->belongsTo(self::class);
    }


    public function parent() {
        return $this->belongsTo('App\Category', 'parent_id');
    }


    public function getParentsAttribute()
    {
        $parents = collect([]);

        $parent = $this->parent;

        while(!is_null($parent)) {
            $parents->push($parent);
            $parent = $parent->parent;
        }

        return $parents;
    }

    public function teaser() {
        return $this->hasMany('App\Teaser');
    }

        

    public static function display() {
        if (Cache::has('category_menu_'.App::getLocale())) {
            $categoryMenu = Cache::get('category_menu_'.App::getLocale());
        } else {
            $categoryMenu = Cache::remember('category_menu_'.App::getLocale(), \Carbon\Carbon::now()->addDays(30), function () {
                $arrCategories = array();
                foreach (\App\Category::with('translations')->whereNull('parent_id')
                            ->where('active', 1)->orderBy('created_at', 'asc')->get() as $category) {
                    $arrCategories[$category->id]['parent'] = array($category->slug, $category->getTranslatedAttribute('name'), $category->image);
                    buildTree($arrCategories, $category);            
                }
                return $arrCategories;
            });
        }
        return new \Illuminate\Support\HtmlString(
            \Illuminate\Support\Facades\View::make('menu.category', ['items' => $categoryMenu])->render()
        );
    }
}

function buildTree(&$arrCategories, $category) {
    //$branch = array();
    if (count($category->childs)) {
        foreach(\App\Category::where('parent_id', '=', $category->id)->orderBy('created_at')->get() as $_category) {
            //$arrCategories[$_category->id]['childs'][] = array($_category->slug, $_category->getTranslatedAttribute('name'), $_category->image);
            $branch[$_category->id]['parent'] = array($_category->slug, $_category->getTranslatedAttribute('name'), $_category->image);
            buildTree($branch, $_category);
            //buildTree($arrCategories, $_category);
        }
        $arrCategories[$category->id]['childs'] = $branch;
    } else {
        //$categoryMenu[$category->id]['childs'] = $branch;
        return $arrCategories;
    }
} 