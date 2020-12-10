<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use TCG\Voyager\Traits\Translatable;
use Illuminate\Support\Facades\Cache;
use TCG\Voyager\Traits\Resizable;


class Room extends Model
{
    use Translatable;

    protected $translatable = ['name', 'description', 'meta_title', 'meta_description'];

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function childs() {
        return $this->hasMany('App\Room','parent_id','id')->orderBy('order') ;
    }

    public function parentId() {
        return $this->belongsTo(self::class);
    }


    public function parent() {
        return $this->belongsTo('App\Room', 'parent_id');
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


    public static function display() {
        if (Cache::has('room_menu_'.App::getLocale())) {
            $roomMenu = Cache::get('room_menu_'.App::getLocale());
        } else {
            $roomMenu = Cache::remember('room_menu_'.App::getLocale(), \Carbon\Carbon::now()->addDays(30), function () {
                $arrRooms = array();
                foreach (\App\Room::with('translations')->where('in_menu', 1)->orderBy('name', 'ASC')->get() as $menu) {
                    $arrRooms[$menu->slug] = (trim($menu->getTranslatedAttribute('name')));
                }

                return $arrRooms;
            });
        }

        return new \Illuminate\Support\HtmlString(
            \Illuminate\Support\Facades\View::make('menu.room', ['items' => $roomMenu])->render()
        );
    }    
}
