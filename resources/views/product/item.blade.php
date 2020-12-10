<div class="container-inner sc-product-item" itemtype="http://schema.org/Product" itemscope>
    <span class="product-loading" style="display: none;"></span>
    <div class="hide" data-name="product_name" data-value="{{ $item->name }}"></div>
    <div class="hide product-id" data-name="product_id" data-value="{{ $item->id }}"></div>
    <div class="hide" data-name="product_slug" data-value="{{ $item->slug }}"></div>

    <div class="product-block-inner">
        <div class="image-block">
            <a itemprop="url" href="{{ url_local($item->slug) }}">
                <img data-src="{{ asset('storage/'.get_thumbnail($item->main_photo, 'mwcropped')) }}"
                     src="{{ asset('storage/'.get_thumbnail($item->main_photo, 'mwcropped')) }}"
                     alt="{{ $item->getTranslatedAttribute('name') .' SKU: '. $item->sku }}"
                     title="{{ $item->getTranslatedAttribute('name') .' SKU: '. $item->sku }}"
                     class="lazy img-fluid"
                     data-name="product_image" 
                     width="400"
                     itemprop="image"
                />
                
            </a>
            
        </div>
        <div class="product-detail-wrapper">
            <h3 class="product-name" itemprop="image" data-name="product_sku" data-value="{{ $item->sku }}"><a href="{{ url_local('product', [$item->category->slug, $item->slug]) }}">{{ $item->name }}</a></h3>
            <span class="brand" itemprop="brand"><a itemprop="url" href="{{ url_local('brand', $item->brand->slug) }}">{{ $item->brand->name }}</a></span>
            <div class="shipping-time in-stock">{{ $product->price ? __('product.in_stock') : __('product.check_price')}}</div>
            @if ($item->price)
                <span itemprop="price" class="price"><b>{{ $item->price ? '€'.number_format($item->price, 2, ',', '.') : '' }}</b></span>
                <span class="vat">incl. VAT</span>
            @endif
        </div>
    </div>




    <div class="tracking-parent">
        <div class="mouse-over rendered done">
            <div class="variants">
                @foreach ($images as $thumb)
                    @if (is_file('storage/'.get_thumbnail($thumb, 'small')))
                    <div class="variant">
                        <img class="hover-main-img" 
                            src="{{ asset('storage/'.get_thumbnail($thumb, 'small')) }}" 
                            cropped-src="{{ asset('storage/'.get_thumbnail($thumb, 'wcropped')) }}"
                            alt="{{ $item->name }}" class="loading" width="55" height="70">
                    </div>
                    @endif
                @endforeach
            </div>
            <div class="details d-flex justify-content-center">
                <div class="detail active price-parent">
                    <a href="{{ url_local($item->slug) }}" class="photo-thumb">
                        <img src="{{ asset('storage/'.get_thumbnail($item->main_photo, 'mwcropped')) }}" 
                            alt="{{ $item->name }}" 
                            class="loading lazy main-previw-container img-fluid">
                        <div class="name"><a href="{{ url_local($item->slug) }}">{{ $item->getTranslatedAttribute('name') }}</a></div>
                        <div class="brand"><a href="{{ url_local('brand', $item->brand->slug) }}">{{ $item->brand->name }}</a></div>
                    </a>
                    <div class="shipping-time in-stock">{{ $product->price ? __('product.in_stock') : __('product.check_price')}}</div>
                    <div class="clearfix"></div>
                    @if ($item->price)
                        <span class="price" data-name="price" data-value="{{ $item->price }}"><b>{{ $item->price ? '€'.number_format($item->price, 2, ',', '.') : '' }}</b></span>
                        <span class="vat">incl. VAT</span>
                        @include('product.options', [
                            'options' => $options, 
                            'id' => $item->id, 
                            'hide' => 'hide', 
                            'allowReload' => 0,
                            'optS' => '',
                            'selectedThumb' => '',
                            'selectedOption' => ''])
                        <div class="product-button d-flex justify-content-center">
                            <div class="control-group">
                                <input type="number" name="quantity" class="form-control quantity" value="1" />
                                <button class="btn btn-order hvr-sweep-to-top sc-add-to-cart" data-id="{{ $item->id }}">
                                    {{ __('product.btn_order') }}
                                </button> 
                            </div>
                        </div>
                    @else
                        @include('product.options', [
                            'options' => $options, 
                            'id' => $item->id, 
                            'hide' => 'hide', 
                            'allowReload' => 0,
                            'optS' => '',
                            'selectedThumb' => '',
                            'selectedOption' => ''])
                        <div class="hide" data-name="price" data-value="{{ $item->price }}"></div>
                        <div class="product-button d-flex justify-content-center">
                            <button class="btn btn-order sc-add-to-cart" data-id="{{ $item->id }}">
                                {{ __('product.btn_checkprice') }}
                            </button>
                        </div>
                    @endif
                </div>                
            </div>
        </div>
    </div>
</div>

