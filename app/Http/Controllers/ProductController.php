<?php
/**
 * Created by PhpStorm.
 * User: Victory
 * Date: 22.12.2018
 * Time: 22:43
 */
namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;

use App\Http\Resources\CategoryCollection;
use App\Http\Resources\Product;

use App\Mail\ProductMissing;
use App\ProductSubscriber;
use App\Option;
use Illuminate\Http\Request;

use App\Http\Resources\Product as ProductResource;
use App\Http\Resources\ProductCollection as ProductCollection;
use App\Http\Resources\Category as CategoryResource;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;
use MetaTag;
use Psr\Log\NullLogger;
use App\ProductReview;


class ProductController extends Controller {

    public function getCategoryStatic($categorySlug, Request $request) {
        $filters = array();
        $selectedOption = $selectedThumb = $optS = '';

        $category = \App\Category::withTranslations(['en'])->where('slug', '=', $categorySlug)->first();
        if (!$category) {
            if (strpos($categorySlug, '.')) {
                $selectedOption = substr($categorySlug, (strpos($categorySlug, '.') + 1));
                $categorySlug = substr($categorySlug, 0, strpos($categorySlug, '.'));
            }
            $product = \App\Product::withTranslations(['en'])->where('slug', '=', $categorySlug)->first();
        }
        if (!$category && !$product) {
            abort(404);
        }

        if ($category) {
            foreach ($category->getParentsAttribute() as $parent) {
                $arrBreadCrumbs[] = $parent->slug.'@'.$parent->getTranslatedAttribute('name');
            }
            $arrBreadCrumbs[] = $category->slug.'@'.$category->getTranslatedAttribute('name');

            // get all parents for breadcrumbs
    
            $cIds[] = $category->id;
            foreach ($category->childs as $cat) {
                $cIds[] = $cat->id;
            }
            $filters['categoryIds'] = $cIds;
            $products = $this->getProductsBySearch($filters, $request->get('perPage') ? $request->get('perPage') : 24, $request->get('sort'));
    
            $data = [
                'products' => $products,
                'category' => $category,
                'breadcrumbs' => $arrBreadCrumbs
            ];
    
            MetaTag::set('title', $category->getTranslatedAttribute('meta_title'));
            MetaTag::set('description', $category->getTranslatedAttribute('meta_description'));
    
            if ($request->isXmlHttpRequest()) {
                $returnHTML = view('product.product-items', compact('products'))->render();
    
                return response()->json(array(
                    'success' => true,
                    'total' => $products->lastPage(),
                    'currentPage' => $request->get('page') ? $request->get('page') : 1,
                    'html' => $returnHTML));
            }
    
            return view('product.products', compact('data'));
        } else {
            
            $product = \App\Product::with('translations')->with('category')->where('slug', $categorySlug)->first();

            if (!$product) {
                abort(404);
            }

            $metaTitle = $product->getTranslatedAttribute('meta_title');
            $metaDescription = $product->getTranslatedAttribute('meta_description');

            $selDataOptions = array();
            if ($selectedOption) {
                //check article or code
                $options = json_decode($product->options);
                foreach ($options as $option) {
                    //dd($option->article);
                    $prices = $option->price;
                    $photos = $option->photo;
                    $articles = $option->article;
                    $ids = $option->selected;
                    $key = array_search($selectedOption, $articles);
                    if ($key === false) {
                    } else {
                        $selDataOptions = array(
                            'price' => $prices[$key],
                            'article' => $articles[$key],
                            'photo' => $photos[$key]
                        );
                        $selectedOptId = $ids[$key];
                        break;
                    }
                }
                $optS = \App\Option::where('id', $selectedOptId)->first();
                $selDataOptions['dboption'] = $optS;
                //$selectedThumb = $optS->image;
            }

            if (count($product->category->getParentsAttribute())) {
                foreach ($product->category->getParentsAttribute() as $parent) {
                    if (!$metaTitle && $parent->getTranslatedAttribute('meta_title')) {
                        $metaTitle = str_replace('%name%', $product->getTranslatedAttribute('name').' '.$product->code, $parent->getTranslatedAttribute('product_title'));
                        $metaDescription = str_replace('%name%', $product->getTranslatedAttribute('name').' '.$product->code, $parent->getTranslatedAttribute('product_description'));
        
                    }
                    $arrBreadCrumbs[] = $parent->slug.'@'.$parent->getTranslatedAttribute('name');
                }
            } else {
                $arrBreadCrumbs[] = $product->category->slug.'@'.$product->category->getTranslatedAttribute('name');    
            }
            $arrBreadCrumbs[] = $product->slug.'@'.$product->getTranslatedAttribute('name').($optS ? ' '.$optS->name : '');    

            // $metaTitle = $metaTitle ? $metaTitle : $product->getTranslatedAttribute('name');
            MetaTag::set('title', $metaTitle ? $metaTitle : $product->getTranslatedAttribute('name'));
            MetaTag::set('description', $metaDescription);
            

            // work with viewd product Ids
            $viewedIds = $request->session()->get('viewedIds') ? json_decode($request->session()->get('viewedIds')) : array();
            $viewedIds[] = $product->id;
            $request->session()->put('viewedIds',json_encode(array_unique($viewedIds)));
            $viewIds = array_diff($viewedIds, array($product->id));
            if (count($viewIds)) {
                $productViewed = \App\Product::whereIn('id', $viewIds)->limit(4)->get();
            } else {
                $productViewed = array();
            }

            // connected products
            $arrConnectedIds = $connected = array();
            foreach(\App\ConnectedProduct::where('owner_id', $product->id)->get() as $connected) {
                $arrConnectedIds[] = $connected->product_id;
            }
            if (count($arrConnectedIds)) {
                $connected = \App\Product::whereIn('id', $arrConnectedIds)->get();
            }else {
                $connected =  \App\Product::where('active', 1)->inRandomOrder()->limit(4)->get();
            }

            //collection product
            $collectionProducts = \App\Product::where('collection_id', $product->collection_id)->inRandomOrder()->limit(4)->get();
            
            $data = [
                'product' => $product,
                'title' => $product->code. ' - '.$product->getTranslatedAttribute('name').__('products.brans').$product->brand->name,
                'breadcrumbs' => $arrBreadCrumbs,
                'thumbs' => json_decode($product->image),
                'photos' => json_decode($product->image),
                'reviews' => \App\ProductReview::where('product_id', $product->id)->where('status', 'ACTIVE')->paginate(5),
                'viewed' => $productViewed,
                'connected' => $connected,
                'collection' => $collectionProducts,
                'profile' => '',
                'selectedOption' => $selDataOptions
            ];
    
            return view('product.show', compact('data'));
    
        }
    }


    public function addToWish(Request $request) {
        dd(Auth::id());
    }


    public function getReview(Request $request) {
        $productId = $request->get('product_id');
        $items = \App\ProductReview::where('product_id', $request->get('product_id'))
            ->where('status', 'ACTIVE')->orderBy('created_at', 'desc')->paginate(5);
        $returnHTML = view('product.review', compact(
            'items',
            'productId'
        ))->render();

        return response()->json(array(
            'success' => true,
            'total' => $items->lastPage(),
            'currentPage' => $request->get('page') ? $request->get('page') : 1,
            'html' => $returnHTML));        
    }


    public function getProductReview(Request $request) {
        $product = \App\Product::where('id', $request->get('product_id'))->first();
        if (!$product) {
            return response()->json(array(
                'success' => false,
            ));  
        } else {
            $options = array();
            if ($request->get('options')) {
                foreach ($request->get('options') as $option) {
                    $opt = \App\Option::where('code', $option['code'])->first();
                    if ($opt) {
                        $options[] = $opt;
                    }
                }
            }
            $item = [
                'product' => $product,
                'options' => $options
            ];
            $returnHTML = view('product.short-review', compact(
                'item',
            ))->render();
            return response()->json(array(
                'success' => true,
                'html' => $returnHTML));        
            }
    }


    public function getVisited(Request $request) {
        $viewedIds = $request->session()->get('viewedIds') ? json_decode($request->session()->get('viewedIds')) : array();
        
        $filters['ids'] = $viewedIds;

        $products = $this->getProductsBySearch($filters, $request->get('perPage') ? $request->get('perPage') : 24, $request->get('sort'));
        $arrBreadCrumbs[0] = 'visited@'.__('page.all_visited');
        

        $data = [
            'products' => $products,
            'breadcrumbs' => $arrBreadCrumbs,
            'isH1' => false
        ];

        if ($request->isXmlHttpRequest()) {
            $returnHTML = view('product.product-items', compact('products'))->render();

            return response()->json(array(
                'success' => true,
                'total' => $products->lastPage(),
                'currentPage' => $request->get('page') ? $request->get('page') : 1,
                'html' => $returnHTML));
        }

        return view('product.visited', compact('data'));                

    }


    public function getBrand($brandSlug, Request $request) {
        $brand = \App\Brand::where('slug', '=', $brandSlug)->first();

        if (!$brand) {
            abort(404);
        }

        MetaTag::set('title', $brand->getTranslatedAttribute('meta_title') ? $brand->getTranslatedAttribute('meta_title') : 'Select interior world by '.$brand->name);
        MetaTag::set('description', $brand->getTranslatedAttribute('meta_description'));

        $filters['brandId'] = $brand->id;
        $products = $this->getProductsBySearch($filters, $request->get('perPage') ? $request->get('perPage') : 24, $request->get('sort'));
        $arrBreadCrumbs[0] = 'brands@'.__('page.all_brands');
        $arrBreadCrumbs[1] = $brand->slug.'@'.$brand->name;

        $data = [
            'products' => $products,
            'category' => $brand,
            'breadcrumbs' => $arrBreadCrumbs,
            'isH1' => false
        ];

        if ($request->isXmlHttpRequest()) {
            $returnHTML = view('product.product-items', compact('products'))->render();

            return response()->json(array(
                'success' => true,
                'total' => $products->lastPage(),
                'currentPage' => $request->get('page') ? $request->get('page') : 1,
                'html' => $returnHTML));
        }

        return view('product.products', compact('data'));                
    }


    public function getDesigner($designerSlug, Request $request) {
        $designer = \App\Designer::where('slug', '=', $designerSlug)->first();

        if (!$designer) {
            abort(404);
        }

        MetaTag::set('title', $designer->getTranslatedAttribute('meta_title') ? $designer->getTranslatedAttribute('meta_title') : 'Select interior world by '.$designer->name);
        MetaTag::set('description', $designer->getTranslatedAttribute('meta_description'));

        $filters['designerId'] = $designer->id;
        $products = $this->getProductsBySearch($filters, $request->get('perPage') ? $request->get('perPage') : 24, $request->get('sort'));
        $arrBreadCrumbs[0] = 'designers@'.__('page.all_designers');
        $arrBreadCrumbs[1] = $designer->slug.'@'.$designer->name;

        $data = [
            'products' => $products,
            'category' => $designer,
            'breadcrumbs' => $arrBreadCrumbs,
            'isH1' => false
        ];

        if ($request->isXmlHttpRequest()) {
            $returnHTML = view('product.product-items', compact('products'))->render();

            return response()->json(array(
                'success' => true,
                'total' => $products->lastPage(),
                'currentPage' => $request->get('page') ? $request->get('page') : 1,
                'html' => $returnHTML));
        }

        return view('product.products', compact('data'));                
    }


    public function getStyle($styleSlug, Request $request) {
        $style = \App\Style::where('slug', '=', $styleSlug)->first();

        if (!$style) {
            abort(404);
        }

        MetaTag::set('title', $style->getTranslatedAttribute('meta_title') ? $style->getTranslatedAttribute('meta_title') : 'Select interior world by '.$style->name);
        MetaTag::set('description', $style->getTranslatedAttribute('meta_description'));

        $filters['styleId'] = $style->id;
        $products = $this->getProductsBySearch($filters, $request->get('perPage') ? $request->get('perPage') : 24, $request->get('sort'));
        $arrBreadCrumbs[0] = $style->slug.'@'.$style->name;

        $data = [
            'products' => $products,
            'category' => $style,
            'breadcrumbs' => $arrBreadCrumbs,
            'isH1' => false
        ];

        if ($request->isXmlHttpRequest()) {
            $returnHTML = view('product.product-items', compact('products'))->render();

            return response()->json(array(
                'success' => true,
                'total' => $products->lastPage(),
                'currentPage' => $request->get('page') ? $request->get('page') : 1,
                'html' => $returnHTML));
        }

        return view('product.products', compact('data'));                
    }



    public function getRoom($roomSlug = '', Request $request) {
        if ($roomSlug) {
            $room = \App\Room::where('slug', '=', $roomSlug)->first();

            if (!$room) {
                abort(404);
            }
            $filters['roomId'] = $room->id;
            $arrBreadCrumbs[0] = 'room@'.__('page.all_room');
            $arrBreadCrumbs[1] = $room->slug.'@'.$room->name;

            MetaTag::set('title', $room->getTranslatedAttribute('meta_title') ? $room->getTranslatedAttribute('meta_title') : 'Designer and Luxury Rooms '.$room->name);
            MetaTag::set('description', $room->getTranslatedAttribute('meta_description'));
        } else {
            $room = '';
            $filters['roomId'] = 'all';
            $arrBreadCrumbs[0] = 'room@'.__('page.all_rooms');
            $roomCarousel = \App\Room::whereNull('parent_id')->orderBy('name')->get();

            MetaTag::set('title', 'Designer and Luxury Rooms');
            MetaTag::set('description', 'Designer and Luxury Rooms');
        }


        $products = $this->getProductsBySearch($filters, $request->get('perPage') ? $request->get('perPage') : 24, $request->get('sort'));

        $data = [
            'products' => $products,
            'category' => $room,
            'breadcrumbs' => $arrBreadCrumbs,
            'carousel' => $roomCarousel,
            'isH1' => false
        ];

        if ($request->isXmlHttpRequest()) {
            $returnHTML = view('product.product-items', compact('products'))->render();

            return response()->json(array(
                'success' => true,
                'total' => $products->lastPage(),
                'currentPage' => $request->get('page') ? $request->get('page') : 1,
                'html' => $returnHTML));
        }

        return view('product.room-products', compact('data'));                
    }


    public function getProductsBySearch($filters, $perPage = 24, $sort = '') {
        $query = \App\Product::select('products.*');
        $query->where('active', 1);

        if (isset($filters['colorIds'])) {
            $query->leftJoin('product2colors', 'products.id', '=', 'product2colors.product_id')->whereIn('product2colors.color_id', $filters['colorIds']);
        }
        if (isset($filters['categoryIds'])) {
            $query->whereIn('category_id', $filters['categoryIds']);
        }
        if (isset($filters['ids'])) {
            $query->whereIn('id', $filters['ids']);
        }
        if (isset($filters['designerId'])) {
            $query->where('designer_id', $filters['designerId']);
        }
        if (isset($filters['brandId'])) {
            $query->where('brand_id', $filters['brandId']);
        }
        if (isset($filters['styleId'])) {
            $query->where('style_id', $filters['styleId']);
        }
        if (isset($filters['roomId'])) {
            if ($filters['roomId'] != 'all')
                $query->where('room_id', $filters['roomId']);
            else
                $query->whereNotNull('room_id');
        }
        

        if ($sort == 'name-asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sort == 'name-desc') {
            $query->orderBy('name', 'desc');
        } else {
            $query->orderBy('updated_at', 'desc');
        }
        return $query->paginate($perPage);
    }


    function reviewSubmit(Request $request) {
        $data = $request->all();
        $validator = \Validator::make($request->all(), [
            'product_id' => 'required',
            'nickname' => 'required',
            'summary' => 'required',
            'review' => 'required',
            'rate' => 'required',
        ]);
     
        if ($validator->passes()) {
            unset($data['_token']);
            ProductReview::insert($data);
            return response()->json(['success' => true]);
        }
     
        return response()->json(['error'=>$validator->errors()->all()]);
    }

    


}
//vi
