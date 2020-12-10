@extends('site')

@section('content')
    <div class="product-view">
        @include('partials.title',['title' => $data['product']->getTranslatedAttribute('name')])

        @include('partials.breadcrumbs',['breadcrumbs' => $data['breadcrumbs']])

        <div class="alert alert-success alert-dismissible fade hide" id="success-msg-{{ $data['product']->id }}" role="alert">
            <span class="message-success-add"></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="row sc-product-item">
            <input type="hidden" value="{{ $data['product']->id }}" id="productId" />
            <div class="hide" data-name="product_slug_main" data-value="{{ $data['product']->slug }}"></div>
            <div class="hide" data-name="product_name" data-value="{{ $data['product']->name }}"></div>
            <div class="hide product-id" data-name="product_id" data-value="{{ $data['product']->id }}"></div>
            <div class="hide" data-name="product_slug" data-value="{{ $data['product']->slug }}"></div>

            <div class="col-lg-7 col-xs-12 col-md-12">
                <div class="photo position-relative">
                    @if (isset($data['selectedOption']['photo']))
                    <a href="{{ asset('storage/'.str_replace('\\', '/', $data['selectedOption']['photo'])) }}" data-lightbox="photoset">
                        <img data-src="{{ asset('storage/'.get_thumbnail($data['selectedOption']['photo'], 'wcropped')) }}"
                             src="{{ asset('storage/'.get_thumbnail($data['selectedOption']['photo'], 'wcropped')) }}"
                             alt="{{ $data['product']->getTranslatedAttribute('name') .' код: '. $data['product']->sku }}"
                             title="{{ $data['product']->getTranslatedAttribute('name') .' код: '. $data['product']->sku }}"
                             class="img-fluid lazyload lazy"
                             data-name="product_image"
                        />
                    </a>
                    <div class="photo-enlarge"><a href="{{ asset('storage/'.str_replace('\\', '/', $data['selectedOption']['photo'])) }}" data-lightbox="photoset"><i class="fas fa-search-plus"></i> Enlarge</a></div>
                    @else
                    <a href="{{ asset('storage/'.str_replace('\\', '/', get_thumbnail($data['photos'][0], 'watermark'))) }}" data-lightbox="photoset">
                        <img data-src="{{ asset('storage/'.get_thumbnail($data['photos'][0], 'watermark')) }}"
                             src="{{ asset('storage/'.get_thumbnail($data['photos'][0], 'watermark')) }}"
                             alt="{{ $data['product']->getTranslatedAttribute('name') .' код: '. $data['product']->sku }}"
                             title="{{ $data['product']->getTranslatedAttribute('name') .' код: '. $data['product']->sku }}"
                             class="img-fluid lazyload lazy"
                             data-name="product_image"
                        />
                    </a>
                    <div class="photo-enlarge"><a href="{{ asset('storage/'.str_replace('\\', '/', get_thumbnail($data['photos'][0], 'watermark'))) }}" data-lightbox="photoset"><i class="fas fa-search-plus"></i> Enlarge</a></div>
                    @endif
                    <br/>
                    <div class="photo-name">{{ $data['product']->getTranslatedAttribute('name') }} {{$data['selectedOption'] ? ' '.$data['selectedOption']['dboption']->name. ' (' .$data['selectedOption']['article']. ')' : ''}}</div>
                </div>
                <div class="thumbs">
                    @foreach($data['thumbs'] as $key => $thumb)
                        @if (\Illuminate\Support\Facades\File::exists('storage/'.str_replace('\\', '/', $data['photos'][$key])))
                            <div class="thumb-img">
                                <a href="{{ asset('storage/'.str_replace('\\', '/', get_thumbnail($data['photos'][$key], 'watermark'))) }}" data-lightbox="photoset">
                                    <img class="img-fluid" src="{{ asset('storage/'.$data['product']->getThumbnail($thumb, 'small')) }}"
                                    alt="{{ $data['product']->brand->name }}"
                                     title="{{ $data['product']->brand->name }}"
                                     />
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="col-lg-5">
                <div class="info">
                    <span class="name" data-name="product_sku" data-value="{{ $data['product']->sku }}">{{ $data['product']->getTranslatedAttribute('name') }} {{$data['selectedOption'] ? ' '.$data['selectedOption']['dboption']->name : ''}}</span>
                    <div class="row">
                        <div class="col-lg-6">
                            <span class="brand" data-name="brand_name" data-value="{{ $data['product']->brand->name }}">by <a href="{{ url_local('brand', $data['product']->brand->slug) }}">{{ $data['product']->brand->name }}</a></span>
                            <span class="brand" data-name="brand_country" data-value="{{ $data['product']->brand->country->getTranslatedAttribute('name') }}">Brand origin country {{ $data['product']->brand->country->getTranslatedAttribute('name') }}</span>
                            <span class="hide" data-name="brand_slug" data-value="{{ $data['product']->brand->slug }}"></span>
                            @if ($data['product']->designer_id)
                                <span class="brand" 
                                    data-name="designer_name" 
                                    data-value="{{ $data['product']->designer->name }}">
                                    Designer: <a href="{{ url_local('designer', $data['product']->designer->slug) }}">{{ $data['product']->designer->name }}</a>
                                </span>
                                <span class="hide" data-name="designer_slug" data-value="{{ $data['product']->designer->slug }}"></span>
                            @endif
                            @if ($data['product']->collection_id)
                                <span class="brand" data-name="collection_name" data-value="{{ $data['product']->collection->name }}">
                                    Collection: <a href="#collection">{{ $data['product']->collection->name }}</a>
                                </span>
                                <span class="hide" data-name="collection_slug" data-value="{{ $data['product']->collection->slug }}"></span>
                            @endif
                        </div>
                        <div class="col-lg-6">
                            <a href="{{ url_local('brand', $data['product']->brand->slug) }}">    
                                <img class="img-fluid brand-logo" src="{{ asset('storage/'.str_replace('\\', '/', $data['product']->brand->logo)) }}"  
                                 alt="{{ $data['product']->brand->name }}"
                                 title="{{ $data['product']->brand->name }}"
                                 />
                            </a>
                        </div>
                    </div>


                    @if ($data['product']->active == 1)
                        <div class="clearfix"><br/></div>
                        @include('product.options', ['options' => json_decode($data['product']->options), 
                            'id' => $data['product']->id, 
                            'hide' => '', 
                            'allowReload' => 1,
                            'optS' => @$data['selectedOption']['dboption'],
                            'selectedOption' => $data['selectedOption'],
                        ])
                        
                        <div class="shipping" data-name="product_shipping" data-value="{{ $data['product']->shipping->getTranslatedAttribute('name') }}">{{ $data['product']->shipping->getTranslatedAttribute('name') }}</div>

                        <div class="sku" 
                            data-name="article" 
                            data-value="{{ isset($data['selectedOption']['article']) ? $data['selectedOption']['article'] : $data['product']->sku }}">
                            Article: {{ isset($data['selectedOption']['article']) ? $data['selectedOption']['article'] : $data['product']->sku }}
                        </div>
                        
                        @if ($data['product']->price > 0)
                            <div class="price" data-name="price" data-value="{{ $data['product']->price }}">€{{ number_format($data['product']->price, 2, ',', '.') }} <span ></span></div>
                            <span class="vat-tips"><a href="{{ url_local('page', 'vat')}}">incl. VAT</a></span>
                        @elseif (isset($data['selectedOption']['price']))
                            <div class="price" data-name="price" data-value="{{ $data['selectedOption']['price'] }}">€{{ number_format($data['selectedOption']['price'], 2, ',', '.') }} <span ></span></div>
                            <span class="vat-tips"><a href="{{ url_local('page', 'vat')}}">incl. VAT</a></span>
                        @else
                            <div class="hide" data-name="price" data-value="{{ $data['product']->price }}"></div>
                        @endif

                        <div class="clearfix"></div>

                        <div class="float-left order-block form-group">
                            <div class="qty-control">
                                <span class="edit-qty minus" onClick="minusQty()">▼</span>
                                <input type="text" class="cart-qty" name="product_quantity" data-name="product_quantity" class="form-control sc-qty-cart" value="1" />
                                <span class="edit-qty plus" onClick="plusQty()">▲</span>
                            </div>
                            @if ($data['product']->price > 0 || isset($data['selectedOption']['price']))
                                <button class="btn btn-order sc-add-to-cart" data-id="{{ $data['product']->id }}">
                                    {{ __('product.btn_order') }}
                                </button>                           
                            @else
                                <button class="btn btn-order sc-add-to-cart" data-id="{{ $data['product']->id }}">
                                    {{ __('product.btn_checkprice') }}
                                </button>                           
                            @endif
                        </div>
                        <div class="product-action-btn">
                            @guest
                                <i class="far fa-heart" onClick="$('#wishMessage').modal('show')"></i>
                            @else
                                <i class="far fa-heart" onClick="addToWhishList('{{ $data['product'] }}')"></i>
                            @endif
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <div class="clearfix"></div>

                        <span class="vat"><span>3%</span> discount on prepayment via advance bank transfer</span>
                        <span class="vat"><a href="#">Price excl. shipping cost</a></span>
                        <span class="delivery"><img src="/images/track.png" width="16" height="16" />Free delivery to Austria on orders over €100.00</span> 
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="our_services">
                                    <i class="fa fa-phone" aria-hidden="true"></i>

                                    <div class="services-phone">Our Service:<br/> <a class="js-content-telephone" data-href="+43 662 26-82-22" href="tel:43662268222">+43 662 26-82-22</a></div>
                                </div>
                                <div class="our_services request-services">
                                    <i class="fa fa-at" aria-hidden="true"></i>

                                    <div class="services-phone request-item"><a class="request-link" href="javascript:;">Request this item</a></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    @else
                        <div class="alert alert-warning" role="alert">
                            {{ __('products.no_available') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
    <input type="hidden" id="reloadPage" value="0" />

    <div class="row">
        <div class="col-lg-12">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#description">Product description</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#pdf">PDF</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#review">Customer review</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#secure">Secure Shopping</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#return">Cancellation & Return</a>
                </li>
                @if (count($data['viewed']))
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#viewed">Resently viewed</a>
                    </li>
                @endif
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="description">
                    <span class="description">{!! str_replace(array("\n", "\r", "[", "]"), '<br/>', $data['product']->getTranslatedAttribute('description')) !!}</span>
                </div>
                <div class="tab-pane fade" id="pdf">

                </div>
                <div class="tab-pane fade" id="return">
                <p>We always strive to ensure the complete satisfaction of all our customers. Should a product you purchase from our online shop not match your needs, Select Interior World abides by a 14-day cancellation policy.</p> 
                </div>
                <div class="tab-pane fade" id="secure">

                </div>
                <div class="tab-pane fade" id="review">
                    <div class="col-lg-7 form-review-conteiner">
                        @include('form.review', ['product_id' => $data['product']->id])
                    </div>
                            
                    <div class="col-lg-12 review-conteiner">
                        @include('product.review', ['productId' => $data['product']->id, 'items' => $data['reviews']])
                    </div>
                </div>
                @if (count($data['viewed']))
                    <div class="tab-pane fade" id="viewed">
                        <div class="products columns-5 grid display-flex row">
                            @foreach($data['viewed'] as $product)
                                <div class="product col-12 col-sm-12 col-md-3 col-lg-4">
                                    @include('product.item', [
                                    'item' => $product,
                                    'allowReload' => 0,
                                    'optS' => '',
                                    'selectedThumb' => '',
                                    'selectedOption' => '',
                                    'options' => json_decode($product->options),
                                    'class' => "col-lg-4 col-md-6 col-6",
                                    'images' => json_decode($product->image)])
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-center">
                            <a href="{{ url_local('visited') }}" class="yellow-btn">View all visited</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>


    


    
    @if (count($data['connected']))
        <div class="row similar-container">
            <div class="col-lg-12">
                <h2 class="fancy"><span>SIMILAR PRODUCTS</span></h2>
                <div class="products columns-5 grid display-flex row" id="products-list">
                @foreach($data['connected'] as $product)
                    <div class="product col-lg-3 col-xl-3 col-md-3 col-sm-12">
                        @include('product.item', [
                        'item' => $product,
                        'options' => json_decode($product->options),
                        'class' => "col-lg-4 col-md-6 col-6",
                        'images' => json_decode($product->image)])
                    </div>
                @endforeach
            </div>
                
            </div>
        </div>
    @endif
    
  
<div class="modal fade" id="emailEnquiry" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Email Enquiry</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="product-email-block"></div>
                @include('form.product-request', ['product_id' => $data['product']->id])
            </div>
        </div>
    </div>
</div>
    

<div class="modal" id="wishMessage" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                You need login first
            </div>
        </div>
    </div>
</div>

@endsection

@section ('javascript')
    <script src="{{ asset('js/jquery-validation/dist/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/lightbox/lightbox.js') }}"></script>
    <script src="{{ asset('js/assets/product/product.js') }}"></script>
    <script src="{{ asset('js/assets/product/rate.js') }}"></script>
@endsection





