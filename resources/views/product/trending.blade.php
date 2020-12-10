
<div class="products columns-5 grid display-flex row" id="products-list">
    @foreach($products as $product)
        <div class="product col-lg-4">
        @include('product.item', [
            'item' => $product,
            'options' => json_decode($product->options),
            'allowReload' => 0,
            'optS' => '',
            'selectedThumb' => '',
            'selectedOption' => '',            
            'images' => json_decode($product->image)])
        </div>
    @endforeach
</div>
<div class="clearfix"></div>
