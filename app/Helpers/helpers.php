<?php

use Illuminate\Contracts\Routing\UrlGenerator;

if (!function_exists('site_menu')) {
    function site_menu($menuName, $type = null, array $options = [])
    {
        return App\MenuType::display($menuName, $type, $options);
    }
}

if (!function_exists('page_menu')) {
    function page_menu($type = null, array $options = [])
    {
        return App\Page::display($type, $options);
    }
}

if (!function_exists('category_menu')) {
    function category_menu($type = null, array $options = [])
    {
        return App\Category::display();
    }
}

if (!function_exists('brand_menu')) {
    function brand_menu($type = null, array $options = [])
    {
        return App\Brand::display();
    }
}

if (!function_exists('designer_menu')) {
    function designer_menu($type = null, array $options = [])
    {
        return App\Designer::display();
    }
}

if (!function_exists('room_menu')) {
    function room_menu($type = null, array $options = [])
    {
        return App\Room::display();
    }
}

if (!function_exists('style_menu')) {
    function style_menu($type = null, array $options = [])
    {
        return App\Style::display();
    }
}

if (!function_exists('month_short_name')) {
    function month_short_name($month) {
        $arrMonth = array('Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек');

        return $arrMonth[$month -1];
    }
}

if (!function_exists('get_thumbnail')) {
    function get_thumbnail($image, $type) {
        // We need to get extension type ( .jpeg , .png ...)
        $ext = pathinfo($image, PATHINFO_EXTENSION);

        // We remove extension from file name so we can append thumbnail type
        $name = str_replace('.'.$ext, '', $image);
        // We merge original name + type + extension
        return $name.'-'.$type.'.'.$ext;

    }
}

if (! function_exists('url_local')) {
    /**
     * Generate a url for the application.
     *
     * @param  string  $path
     * @param  mixed   $parameters
     * @param  bool|null    $secure
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    function url_local($path = null, $parameters = [], $secure = null)
    {
        if (getCountry() == 'at' && App::getLocale() == 'en') {

        } else {
            $path = getCountry().'_' .App::getLocale(). '/'.$path;
        }
        /*if (App::getLocale() == 'ru') {
            $path = 'ru/'.$path;
        }*/
        // exit;
        if (is_null($path)) {
            return app(UrlGenerator::class);
        }

        return app(UrlGenerator::class)->to($path, $parameters, $secure);
    }
}

if (! function_exists('url_changed')) {
    /**
     * Generate a url for the application.
     *
     * @param  string  $path
     * @param  mixed   $parameters
     * @param  bool|null    $secure
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    function url_changed($path = null, $parameters = [], $secure = null)
    {
        
        if (App::getLocale() == 'en' && getCountry() == 'at') {
            $newPath = $parameters != 'at_en' ? $parameters.'/'.$path : $path;
        } else {
            if ($parameters == 'at_en') {
                $newPath = str_replace(getCountry().'_'.App::getLocale(), '', $path);
            } else {
                $newPath = str_replace(getCountry().'_'.App::getLocale(), $parameters, $path);
            }
        }
        if (is_null($newPath)) {
            return app(UrlGenerator::class);
        }

        return app(UrlGenerator::class)->to($newPath, [], $secure);
    }
}

if (! function_exists('alternate_catalog_url')) {
    /**
     * Generate a url for the application.
     *
     * @param  string  $path
     * @param  mixed   $parameters
     * @param  bool|null    $secure
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    function alternate_catalog_url($path = null, $lang = [], $secure = null)
    {
        $queryParams = explode('/', str_replace('ru/', '', $path));
        $alternateQueryParams = array();

        $langAlternate = $lang == 'uk' ? 'ru' : 'uk';

        $slug = $subSlug = $genderSlug = $transSlug = $transSubSlug = $productCode = '';
        if (in_array($queryParams[0], array('zhinochi', 'cholovichi', 'uniseks', 'zhenskie', 'muzhskie'))) {
            $gender = \App\GenderType::whereTranslation('slug_trans', $queryParams[0])->first();
            if (!$gender) {
                return array();
            }
            $genderSlug = $gender->getTranslatedAttribute('slug_trans', $langAlternate);
            $slug = $queryParams[0];
            if (isset($queryParams[1])) {
                $subSlug = $queryParams[1];
            }
        } elseif ($queryParams[0] == 'product') {
            $slug = $queryParams[1];
            $productCode = $queryParams[2];
        } else {
            $slug = $queryParams[0];
            if (isset($queryParams[1])) {
                $subSlug = $queryParams[1];
            }
        }

        if ($slug) {
            $category = \App\Category::whereTranslation('slug_new', $slug)->first();
            if (!$category) {
                return array();
            }
            $transSlug = $category->getTranslatedAttribute('slug_new', $langAlternate);
        }

        if ($subSlug) {
            $category = \App\Category::whereTranslation('slug_new', $subSlug)->first();
            if (!$category) {
                return array();
            }
            $transSubSlug = $category->getTranslatedAttribute('slug_new', $langAlternate);
        }
        if ($genderSlug) {
            $alternateQueryParams[] = $genderSlug;
            $alternateQueryParams[] = $transSlug;
            $alternateQueryParams[] = $transSubSlug;
        } else {
            $alternateQueryParams[] = $transSlug;
            $alternateQueryParams[] = $transSubSlug;
        }

        $alternateQueryParams = array_filter($alternateQueryParams);
        if ($langAlternate == 'ru') {
            $arrUrls['uk'] = $path;
            $arrUrls['ru'] = 'ru/'.implode('/', $alternateQueryParams);
        } else {
            $arrUrls['ru'] = $path;
            $arrUrls['uk'] = implode('/', $alternateQueryParams);
        }

        return $arrUrls;
    }
}




if (! function_exists('alternate_site_url')) {
    /**
     * Generate a url for the application.
     *
     * @param  string  $path
     * @param  mixed   $parameters
     * @param  bool|null    $secure
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    function alternate_site_url($path = null, $lang = [], $secure = null)
    {
        $queryParams = explode('/', str_replace(array('ru/', 'ru'), '', $path));
        $clearPath = str_replace(array('ru/', 'ru'), '', $path);
        $queryParams = array_filter($queryParams);

        $langAlternate = $lang == 'uk' ? 'ru' : 'uk';


        if (isset($queryParams[0]) && $queryParams[0] == 'product') {
            $category = \App\Category::whereTranslation('slug_new', $queryParams[1])->first();
            if (!$category) {
                return array();
            }
            $transSlug = $category->getTranslatedAttribute('slug_new', $langAlternate);
            $alternateQueryParams = array(
                'product',
                $transSlug,
                $queryParams[2]
            );
            if ($langAlternate == 'ru') {
                $arrUrls['uk'] = '/'.$path;
                $arrUrls['ru'] = '/ru/'.implode('/', $alternateQueryParams);
            } else {
                $arrUrls['ru'] = '/'.$path;
                $arrUrls['uk'] = '/'.implode('/', $alternateQueryParams);
            }
        } else {
            if ($clearPath != '/' && $clearPath) {
                $arrUrls['uk'] = '/'.$clearPath;
                $arrUrls['ru'] = '/ru/'.$clearPath;
            } else {
                $arrUrls['uk'] = $clearPath;
                $arrUrls['ru'] = '/ru';
            }
        }
        return $arrUrls;
    }
}

if (! function_exists('canonical_url')) {
    /**
     * Generate a url for the application.
     *
     * @param  string  $path
     * @param  mixed   $parameters
     * @param  bool|null    $secure
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    function canonical_url($path = null, $lang = [], $secure = null)
    {
        $queryParams = explode('/', str_replace(array('ru/', 'ru'), '', $path));
        $clearPath = str_replace(array('ru/', 'ru'), '', $path);
        $arrGender = array('zhinochi', 'cholovichi', 'uniseks', 'zhenskie', 'muzhskie');
        $arrGenderReplacement = array('zhinochi/', 'cholovichi/', 'uniseks/', 'zhenskie/', 'muzhskie/');
        //dd($queryParams);
        $queryParams = array_filter($queryParams);
        if (count($queryParams) == 3 && in_array($queryParams[0], $arrGender)) {
            return str_replace($arrGenderReplacement, '', \Illuminate\Support\Facades\Request::url());
        } elseif (count($queryParams) == 2 && $queryParams[0] == 'uniseks') {
            return str_replace('/uniseks', '', \Illuminate\Support\Facades\Request::url());
        }
        else {
            return \Illuminate\Support\Facades\Request::url();
        }
    }
}

if (! function_exists('getCountry')) {
    /**
     * Get the current application locale.
     *
     * @return string
     */
    function getCountry()
    {
        return Config::get('app.country');
    }
}



if (! function_exists('setCountry')) {
    /**
     * Set the current application locale.
     *
     * @param  string  $locale
     * @return void
     */
    function setCountry($country)
    {
        Config::set('app.country', $country);
    }
}

if (! function_exists('getCurrency')) {
    /**
     * Get the current application locale.
     *
     * @return string
     */
    function getCurrency()
    {
        if (!Session::get('currency')) {
            Session::put('currency', Config::get('app.currency'));
        }

        return Session::get('currency');
    }
}



if (! function_exists('setCurrency')) {
    /**
     * Set the current application locale.
     *
     * @param  string  $locale
     * @return void
     */
    function setCurrency($currency)
    {
        Session::put('currency', $currency);
        

        //Config::set('app.currency', $currency);
    }
}
