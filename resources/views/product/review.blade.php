@if (count($items->items()))
  
    @foreach($items->items() as $key => $item)
        <div class="review">
            <div class="rate-row">
                <div class="saved-review-rate" data-rating="{{ $item->rate }}"></div>
            </div>

            <span class="review-date">{{ \Carbon\Carbon::parse($item->created_at)->format('M d Y')}}</span>  <span class="nickname">{{ $item->nickname }}</span>
            
            <div class="review-content">{!! $item->review !!}</div>
        </div>
    @endforeach
    <div class="d-flex justify-content-center">
        @include('product.paginator', ['paginator' => $items, 'productId' => $productId])
    </div>
@endif