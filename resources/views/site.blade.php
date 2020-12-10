<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token()  }}">
    <script>window.Laravel = { csrfToken : '{{ csrf_token() }}' }</script>
    <link rel="shortcut icon" href="/images/index.ico" type="image/x-icon" />
    <title>{{ MetaTag::get('title') }}</title>
    {!! MetaTag::tag('description') !!}
    {!! MetaTag::tag('robots') !!}
    <link rel="canonical" href="{{ url(Request::url()) }}">
    
    <meta property="og:title" content="{{ MetaTag::get('title') }}" />
    <meta property="og:url" content="{{ url(Request::url()) }}" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="/images/favicon.png" />
    <meta property="og:image:type" content="image/png" />
    <meta property="og:image:width" content="30" />
    <meta property="og:image:height" content="30" />
    <meta name="twitter:image:src" content="/images/favicon.png">

    <link href="{{ asset('css/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/lightbox/lightbox.css') }}" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('js/lazy-load-xt/jquery.lazyloadxt.spinner.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('js/bootstrap-select/dist/css/bootstrap-select.min.css') }}">    
</head>
<body>


<div class="shadow hide modal-backdrop" id="shadow-site"></div>
<div id="root">
    <div class="site-content">
        <header>
            <div class="position-relative gray-line">
                <div class="clearfix header-top">
                    <div class="container">
                        <div class="slogan">FREE SHIPPING TO EUROPEAN COUNTRIES</div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div class="contact-header" style="width: 100%;">
                <div class="container yellow-border">
                    <div class="row">
                        <div class="col-6">
                            <ul class="contact-information">
                                <li><a href="#"><i class="fas fa-phone-alt"></i><span>&nbsp;&nbsp;+43 662 26-82-22</span></a></li>
                                <li><a href="#" onClick="$('#contactRequest').modal('show')"><i class="far fa-envelope"></i><span>&nbsp;&nbsp;Contact</span></a></li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul class="social-information">
                                <li><a href="https://www.instagram.com/select.interiorworld/" target="_blank"><i class="fab fa-instagram"></i></a></li>
                                <li><a href="https://www.facebook.com/select.interiorworld/" target="_blank"><i class="fab fa-facebook"></i></a></li>
                                <li><a href="https://www.pinterest.com/selectinteriorworld/" target="_blank"><i class="fab fa-pinterest-square"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-fixed">
                <div class="container">
                    <div class="row position-relative">
                        <div class="col-lg-3 col-xl-3">
                            <div class="logo-container"><a href="{{ url_local('/') }}"><img src="/images/logo.png" class="logo"></a></div>
                            <div class="selector-container">
                                @include('partials.selector')
                            </div>
                        </div>
                        <div class="col-lg-6 col-xl-6 category-menu-main">
                            {{ category_menu() }}
                        </div>
                        <div class="col-lg-3 col-xl-3 header-cart">
                            <ul class="profile-information">
                                @guest
                                    <li><a href="{{ route('login') }}"><i class="far fa-user"></i>&nbsp;&nbsp;Account</a></li>
                                @else
                                    <li><a href="#"><i class="far fa-user"></i>&nbsp;&nbsp;{{ Auth::user()->name }}</a></li>
                                @endif
                                <li><a href="{{ url_local('wishlist') }}"><i class="far fa-heart"></i>&nbsp;&nbsp;Wish List</a></li>
                                <li id="smartcart" class="position-relative cart togg">
                                    <a href="javascript:;" class="sc-open-cart"><i class="fas fa-shopping-cart"></i>&nbsp;&nbsp;Cart</a>
                                    <span class="cart-counter sc-cart-counter">0</span>
                                    <div class="cart-menu">
                                        <div class="scrolled-content-1">
                                            <div class="close-menu sc-close-cart"></div>
                                            <div class="cart-content sc-cart-content">
                                                
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="search-heder">
                <div class="container">
                    <div class="d-flex d-flex justify-content-center">
                        <form class="top-search col-lg-6">
                            <div class="input-group">
                                <input type="text" class="form-control" aria-label="Dollar amount (with dot and two decimal places)">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </header>
        <main>
            
            @yield('slider')
            
            <div class="container">

                @yield('content')

            </div>
        </main>        
        <div id="scroller"></div>
    </div>
    <div class="push"></div>
    <footer>
        <div class="container">
            <div class="justify-content-center form-content">
                <span>Designer Lighting for Home and Office<br/></span>
                <span class="big-text">NEWSLETTER SIGN UP</span>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-lg-6 col-xl-6">
                    <form class="form-subscribe">
                        <div class="input-group">
                            <input type="text" class="form-control" aria-label="Dollar amount (with dot and two decimal places)">
                            <div class="input-group-append">
                                <span class="input-group-text">SUBSRIBE</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row column-footer">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-3 address">
                        select-interiorworld.com<br/>
                        By<br/> SELECT Baubedarf und<br/> Handelsges.m.b.H.

                        <p><i class="fas fa-map-marker-alt"></i>&nbsp;Warwitzstrasse 9,<br/> Businesscenter SAM<br/> A-5020 , Salzburg , Austria</p>
                        <p>Year of foundation: 2000</p>
                        <p>Commercial registry Austria:<br/> 196241s</p>
                        <p>VAT number AUSTRIA:<br/> ATU50092803</p>
                        <p>VAT number Germany:<br/> DE316548092</p>
                        <p><i class="fas fa-phone-alt"></i>&nbsp;&nbsp;+43 662 26-82-22</p>
                        <ul class="social-information">
                            <li><a href="https://www.instagram.com/select.interiorworld/" target="_blank"><i class="fab fa-instagram"></i></a></li>
                            <li><a href="https://www.facebook.com/select.interiorworld/" target="_blank"><i class="fab fa-facebook"></i></a></li>
                            <li><a href="https://www.pinterest.com/selectinteriorworld/" target="_blank"><i class="fab fa-pinterest-square"></i></a></li>
                        </ul>
                    </div>
                        <div class="col-lg-3 address">
                            {{ site_menu('customers-info') }}
                        </div>
                        <div class="col-lg-3 address">
                            {{ site_menu('company-info') }}
                            <br/>
                            <b>Quick links</b><br/>
                            <ul class="navbar-nav">
                                @guest
                                    <li class="nav-item"><a href="{{ route('login') }}" class="nav-link"><i class="fas fa-user"></i> My account</a></li>
                                @else
                                    <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-user"></i> My account</a></li>
                                @endif
                                <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-shopping-cart"></i> Orders Tracking</a></li>
                                <li class="nav-item"><a href="#" class="nav-link"><i class="far fa-heart"></i> Favorites</a></li>
                                <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-chart-bar"></i> Planning</a></li>
                            </ul>
                        </div>
                        <div class="col-lg-3 address">
                            <b>Shop by</b><br/>
                            <ul class="navbar-nav">
                                <li class="nav-item"><a href="{{ url_local('brands') }}" class="nav-link">Shop by Brands</a></li>
                                <li class="nav-item"><a href="{{ url_local('rooms') }}" class="nav-link">Shop by Rooms</a></li>
                                <li class="nav-item"><a href="{{ url_local('designers') }}" class="nav-link">Shop by Designers</a></li>
                                <li class="nav-item"><a href="{{ url_local('styles') }}" class="nav-link">Shop by Styles</a></li>
                            </ul>
                            <br/>
                            <b>Top categories</b>
                            @php $items = Cache::get('category_menu_'.App::getLocale());@endphp
                            <ul class="navbar-nav">
                                @foreach ($items as $item)
                                    <li><a href="{{ url_local($item['parent'][0]) }}" class="nav-link"><span>{{ $item['parent'][1] }}</span></a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row copyright-payment-container">
                <div class="col-12 payment-merchant">
                    <div class="float-left p-1">
                        We are an audited merchant: <img src="/images/seal.digicert.com.png"  style="max-height: 35px;" />
                    </div>
                    <div class="float-right p-2">
                        Reliable shipping: <img src="/images/ico-footer_delivered.png" style="max-height: 35px;" />
                    </div>
                </div>
                <div class="copyright">Copyright 2000 - 2020 select-interiorworld.com By SELECT Baubedarf und Handelsges.m.b.H. , all rights reserved , are registered trademarks.</div>
            </div>
        </div>
    </footer>    
</div>

<div class="modal fade selector-modal" id="selector-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-selector">
            <div class="modal-body">
                @include('partials.selector-dropdown')
            </div>
        </div>
    </div>
</div>

<div class="modal fade preference" id="contactRequest" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Contact information</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="product-email-block">
            <div class="contact-form-content_cms"><p><strong><span style="font-size: small;">Do you need support? Our team is available to help answer questions.</span></strong></p>
<p>We will get back to you within 24 hours.<br> Monday through Saturday (except Austrian public holidays)</p>
<p><strong><span style="font-size: small;">Email Us:</span></strong> Simply send us an email at info@select-interiorworld.com or fill in the form below.</p>
<p><span style="font-size: small;"><strong>Call Us:</strong></span> +43 662 26-82-22</p></div>                
            </div>
            @include('form.contact-request', ['product_id' => 1])
        </div>
    </div>
</div>

<script src="{{ asset('js/jquery/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('js/jquery/jquery.cookie.js') }}"></script>
<script src="{{ asset('js/bootstrap/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/lazy-load-xt/jquery.lazyloadxt.min.js') }}"></script>
<script src="{{ asset('js/font-awasome/font.js') }}"></script>
<script src="{{ asset('js/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('js/jquery-pagination/jquery.bootpag.js') }}"></script>
<link href="{{ asset('js/scrollbar/jquery.mCustomScrollbar.css') }}" rel="stylesheet">
<script src="{{ asset('js/scrollbar/jquery.mCustomScrollbar.concat.min.js') }}"></script>

<script src="{{ asset('js/assets/app.js') }}"></script>
<script src="{{ asset('js/assets/cart.js') }}"></script>
<script>
    $(document).ready(function(){
        App.init();
        $('#smartcart').smartCart({
            lang: {
                cartEmpty: '{{ __('products.cart_empty') }}',
                cartProducts: '{{ __('products.products') }}',
                cartAmount: '{{ __('products.amount') }}',
                checkoutBtn: '{!! __('products.btn_cart_order') !!}',
                checkoutCartHeader: '{{ __('products.cart_products_header') }}'
            }
        });        
    });
</script>

@yield('javascript')
@stack('javascript')


</body>
</html>
