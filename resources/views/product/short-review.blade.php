<div class="modal-container-header">Product information</div>
<div class="checkout-data">
    <div class="product-item sc-cart-item row">
        <div class="col-md-3 col-lg-3 col-xl-3 product-image">
            <span><a href="{{ url_local($item['product']->slug) }}"><img src="{{ asset('storage/'.get_thumbnail($item['product']->main_photo, 'cropped')) }}" class="img-fluid" /></a></span>
        </div>
        <div class="col-md-3 col-lg-9 col-xl-9 product-data">
            <div class="item-data">
                <div class="part-1">
                    <span class="name"><a href="{{ url_local($item['product']->slug) }}">{{ $item['product']->getTranslatedAttribute('name') }}</a></span>
                    <span class="brand">by <b>{{ $item['product']->brand->name}}</b></span>
                    @if ($item['product']->brand->country_id)
                        <span class="brand">Brand origin country <b>{{ $item['product']->brand->country->name}}</b></span>
                    @endif
                    @if ($item['product']->designer_id)
                        <span class="brand">Designer: <b>{{ $item['product']->designer->name}}</b></span>
                    @endif
                    @if ($item['product']->collection_id)
                        <span class="brand">Collection: <b>{{ $item['product']->collection->name}}</b></span>
                    @endif
                    @if (@item['options'])
                        <div class="options">
                            <ul>
                            @foreach ($item['options'] as $option)
                                @if ($option->image)
                                    <li><span class="option-label">{{ $option->group->name }}</span><img src="{{ asset('storage/'.get_thumbnail($option->image, 'cropped')) }}" /><span class="option-name">{{ $option->name }}</span><span class="clearfix"></span></li>
                                @endif
                            @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($item['product']->price)
                        <span class="item-price"><b>€{{ $item['product']->price }}</b></span>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</div>