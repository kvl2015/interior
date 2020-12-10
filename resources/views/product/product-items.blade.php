
<div>
    <div class="row">
        <div class="col-12 col-lg-6">
            <select name="sourcesPage" id="sourcesPage" class="select-css per-page" placeholder="Per page">
                <option value="24">24</option>
                <option value="48">48</option>
                <option value="100">100</option>
            </select>
            <select name="sourcesSort" id="sourcesSort" class="select-css sorting-select" placeholder="Sorting">
                <option value="">No sorting</option>
                <option value="name-asc">Name (A-Z)</option>
                <option value="name-desc">Name (Z-A)</option>
            </select>
        </div>
        <div class="col-12 col-lg-6">
            <div class="demo4_top float-right" id="pag-top"></div>
        </div>
    </div>
    <div class="clearfix"></div>    
    @if (count($products->items()))
        <div class="products columns-5 grid display-flex row" id="products-list">
            @foreach($products->items() as $product)
                <div class="product col-12 col-sm-12 col-md-3 col-lg-4">
                    @include('product.item', [
                    'item' => $product,
                    'options' => json_decode($product->options),
                    'class' => "col-lg-4 col-md-6 col-6",
                    'images' => json_decode($product->image)])
                </div>
            @endforeach
        </div>
    @else
        <div class="col-12"><p class="no-record">{{ __('product.no_found') }}</p></div>
    @endif
    <div class="clearfix"></div>

    <div class="row">
        <div class="col-lg-6">
            <select name="sourcesPage" id="sourcesPageBottom" class="select-css per-page" placeholder="Per page">
                <option value="24">24</option>
                <option value="48">48</option>
                <option value="100">100</option>
            </select>
            <select name="sourcesSort" id="sourcesSortBottom" class="select-css sorting-select" placeholder="Sorting">
                <option value="">No sorting</option>
                <option value="name-asc">Name (A-Z)</option>
                <option value="name-desc">Name (Z-A)</option>
            </select>
        </div>
        <div class="col-lg-6">
            <div class="demo4_top float-right" id="pag-bottom"></div>
        </div>
    </div>

    <div class="clearfix"></div>
</div>

