@extends('site')

@section('slider')
<div id="carouselExampleIndicators" class="carousel slide home-carousel" data-ride="carousel">
        <ol class="carousel-indicators">
            @for($i =0; $i < count($data['teasers']); $i++)
                <li data-target="#carouselExampleIndicators" data-slide-to="{{$i}}" class="{{$i == 0 ? 'active' : ''}}"></li>
            @endfor
        </ol>
        <div class="carousel-inner">
            @foreach($data['teasers'] as $key => $teaser)
                <div class="carousel-item {{$key == 0 ? 'active' : ''}}">
                    @if ($teaser['parent_category_slug'])
                        <a href="{{ url_local($teaser['parent_category_slug'], [$teaser['category_slug']]) }}">
                            <div style="background: url({{ asset('storage/'.str_replace('\\', '/', $teaser['image'])) }}) no-repeat center;" class="carousel-img-content"></div>
                        </a>
                    @else
                        <a href="{{ url_local($teaser['parent_category_slug']) }}">
                            <div style="background: url({{ asset('storage/'.str_replace('\\', '/', $teaser['image'])) }}) no-repeat center;" class="carousel-img-content"></div>
                        </a>
                    @endif
                </div>
            @endforeach

        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
@endsection

@section('content')
    <div class="d-flex justify-content-center w-100 preference-main">
        <div class="row">
            @php
                $key = 0;
                $preference = $data['preferences'][0];
            @endphp
            <div class="col-12 {{ $key > 0 ? 'hide' : ''}} preference-block">
                <div class="slide-container position-relative">
                    <img id="myImgId" src="{{ asset('storage/'.str_replace('\\', '/', $preference->image)) }}" class="lazy img-fluid" />
                    
                    
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center w-100">
        <div class="row">
            <div class="col-12">
                <a href="javascript:;" class="preferences-btn" onClick="$('#preferenceModal').modal('show')">OUR PREFERENCES</a>
            </div>
        </div>
    </div>
    
    <div class="row trandign">
        <div class="col-lg-12 col-xl-12">
            <h2 class="fancy"><span>TRENDING</span></h2>
        </div>
    </div>

    @include('product.trending', ['products' => $data['trending']])

    <div class="row trandign brands">
        <div class="col-lg-12 col-xl-12">
            <h2 class="fancy"><span>BRANDS</span></h2>
        </div>
    </div>

    <div class="brands-block row">
        <div class="col-12 d-flex align-items-center">
            <div class="owl-carousel owl-theme">
                @foreach($data['brands'] as $brand)
                    <div><a href="{{ url_local('brand', $brand->slug) }}"><img class="img-fluid" src="{{ asset('storage/'.str_replace('\\', '/', $brand->logo)) }}"  alt="{{ $brand->name }}"></a></div>
                @endforeach
            </div>
        </div>
        <div style="display: block;margin: 0px auto;">
            <span><a href="{{ url_local('brands') }}" class="yellow-btn">All brands</a></span>
        </div>
    </div>
    
    <div class="row trandign">
        <div class="col-lg-12 col-xl-12">
            <h2 class="fancy"><span>OUR TOP CATEGORY</span></h2>
        </div>
    </div>
    <div class="top-categories">
        <div class="row">
        @foreach ($data['categories'] as $key => $category)
            @if ($key%3 == 0)
                <div class="col-lg-6">
                    <div class="panel">
                        <div class="homepage-category-container" style="background-image: url({{ asset('storage/'.str_replace('\\', '/', $category->image)) }})">
                            <a href="{{ url_local($category->slug) }}"></a>
                        </div>
                        <div class="bottom-center">
                            <a class="shop-now" href="{{ url_local($category->slug) }}">Shop now</a>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-lg-3">
                    <div class="panel">
                        <div class="homepage-category-container" style="background-image: url({{ asset('storage/'.str_replace('\\', '/', $category->image)) }})">
                            <a href="{{ url_local($category->slug) }}"></a>
                        </div>
                        <div class="bottom-center">
                            <a class="shop-now" href="{{ url_local($category->slug) }}">Shop now</a>
                        </div>                        
                    </div>
                </div>
            @endif
        @endforeach
        </div>
    </div>
    <div class="clearfix"></div>
    
   <div class="main-text">
        <div class="text-content">{!! $data['page']->getTranslatedAttribute('body') !!}</div>
    </div>

</div>

<div class="modal fade preference" id="preferenceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Our Preferences</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="d-flex justify-content-center w-100" style="display:none;">
                <div class="row">
                    @foreach ($data['preferences'] as $key => $preference)
                        <div class="col-12 {{ $key > 0 ? 'hide' : ''}} preference-block" id="preference_{{ $preference->id }}">
                            <div class="slide-container position-relative">
                                <img id="myImgId" src="{{ asset('storage/'.str_replace('\\', '/', $preference->image)) }}" class="lazy img-fluid" />
                                
                                
                            </div>
                        </div>
                    @endforeach
                    <div class="col-12">
                        @foreach ($data['preferences'] as $key => $preference)
                            <a href="javascript:;" onClick="$('.preference-block').hide('slow');$('#preference_{{ $preference->id }}').removeClass('hide').show('slow')" class="preference-page">{{ $key + 1 }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="clearfix visible-xs-block"></div>
        </div>
    </div>
</div>

@endsection



@section('javascript')
    <link href="{{ asset('js/owl.carousel/owl.carousel.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/owl.carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/assets/home/home.js') }}"></script>
@endsection