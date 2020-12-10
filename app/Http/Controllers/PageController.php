<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

use App\Http\Resources\Page as PageResource;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactRequest;
use Illuminate\Support\Facades\Cache;
use App\Assets\ViberApi;
use MetaTag;



class PageController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * get active page items
     */
    public function fetchMenu() {
        $menuItems = \App\Page::where([
                ['active', '=', '1'],
                ['top_menu', '=', '1'],
            ])->orderBy('order', 'asc')->get();

        return PageResource::collection($menuItems);
    }

    public function getMain($locale = '') {
        if (Cache::has('teaser_'.App::getLocale())) {
            $arrTeasers = Cache::get('teaser_'.App::getLocale());
        } else {
            $arrTeasers = Cache::remember('teaser_'.App::getLocale(), \Carbon\Carbon::now()->addDays(30), function () {
                $teasers = array();
                foreach (\App\Teaser::where('active', 1)->orderBy('order', 'asc')->get() as $key => $teaser) {
                    $teasers[] = array(
                        'image' => $teaser->image,
                        'category_slug' => $teaser->category ? $teaser->category->slug : '',
                        'parent_category_slug' => ''
                        //'parent_category_slug' => $teaser->category ? $teaser->category->parent->slug : ''
                    );
                }

                return $teasers;
            });
        }

        $page = \App\Page::whereTranslation('slug', 'main')->first();

        MetaTag::set('title', $page->getTranslatedAttribute('meta_title'));
        MetaTag::set('description', $page->getTranslatedAttribute('meta_description'));
        
        // get main menu categories by order
        $mainCategory = \App\MainCategory::where('id', 1)->first();
        foreach (json_decode($mainCategory->order) as $menu) {
            $menuItems[] = \App\Category::where('id', '=', $menu->id)->first();
        }        
        
        
        $data = [
            'page' => $page,
            'teasers'  => $arrTeasers,
            'categories' => $menuItems,
            'trending' => \App\Product::where('trending', 1)->where('active', 1)->inRandomOrder()->limit(8)->get(),
            'brands' => \App\Brand::whereNotNull('logo')->limit(8)->get(),
            'preferences' => \App\Preference::where('active', 1)->get()
        ];

        return view('page.main', compact('data'));
    }

    public function viewPageStatic($slug, Request $request) {
        //setCountry('be');
        //dd($slug);
        $page = \App\Page::whereTranslation('slug', $slug)->first();
        //echo app()->getLocale();

        MetaTag::set('title', $page->getTranslatedAttribute('meta_title'));
        MetaTag::set('description', $page->getTranslatedAttribute('meta_description'));

        $data = [
            'page' => $page,
            'slug' => $slug
        ];
        return view('page.view', compact('data'));
    }



    public function viewPage($slug) {
        $page = \App\Page::whereTranslation('slug_trans', $slug)->first();
        /*if (!$page) {
            return response()->json(array('success' => true), errors);
        }*/

        $page->page_header = $page->getTranslatedAttribute('page_header', App::getLocale());
        $page->page_content = $page->getTranslatedAttribute('page_content', App::getLocale());
        $page->meta_title = $page->getTranslatedAttribute('meta_title', App::getLocale());
        $page->meta_keyword = $page->getTranslatedAttribute('meta_keyword', App::getLocale());
        $page->meta_description = $page->getTranslatedAttribute('meta_description', App::getLocale());

        return response()->json(array('page' => array($page), 'success' => true) ,200);
    }

    /*public function view($slug) {
        $page = \App\Page::whereTranslation('slug_trans', $slug)->first();
        if (!$page) {
            return response()->json(array('success' => true), 404);
        }

        $page->page_header = $page->getTranslatedAttribute('page_header', App::getLocale());
        $page->page_content = $page->getTranslatedAttribute('page_content', App::getLocale());
        $page->meta_title = $page->getTranslatedAttribute('meta_title', App::getLocale());
        $page->meta_keyword = $page->getTranslatedAttribute('meta_keyword', App::getLocale());
        $page->meta_description = $page->getTranslatedAttribute('meta_description', App::getLocale());

        return response()->json(array('page' => array($page), 'success' => true) ,200);
    }*/

    public function sendQuickRequest(Request $request) {
        $orderInfo = $request->all();

        $viberApi = new ViberApi();
        $viberApi->viberBroadcastQuickRequest($orderInfo);

        $response = [
            'success' => true,
            'message' => 'Ваш запит успішно надіслано'
        ];

        return response()->json($response,200);
    }


    public function sendRequest(Request $request) {
        // $orderInfo = $request->get('data');
        $orderInfo = $request->all();

        Mail::to('lavoks2015@gmail.com')
            ->send(new ContactRequest($orderInfo));


        $response = [
            'success' => true,
            'message' => 'Ваш запит успішно надіслано'
        ];

        return response()->json($response,200);
    }

    public function page404(Request $request) {
        return response()->view('errors', array())->setStatusCode(404);
    }

    public function page404ru(Request $request) {
        return response()->view('404ru', array())->setStatusCode(404);
    }
}
