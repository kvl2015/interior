<form method="POST" class="review-form" id="send-review-form" onsubmit="return false">
    @csrf
    <div class="rate-row">
        <div class="review-rate"></div>
        <div class="error error-rate"></div>
    </div>

    @include('form.fields.text-input',['name' => 'nickname', 'label' => __('form.nickname'), 'value' => '', 'type' => 'text', 'required' => true])

    @include('form.fields.phone-input',['name' => 'summary', 'label' => __('form.summary'), 'value' => '', 'type' => 'text', 'required' => true])

    @include('form.fields.textarea',['name' => 'review', 'label' => __('form.review'), 'value' => '', 'required' => true])

    <input type="hidden" name="product_id" value="{{ $product_id }}" />
    <input type="hidden" name="rate" value="" />

    <button type="submit" class="btn btn-info hvr-sweep-to-top" id="sendReview" value="{{ __('form.review_btn') }}">{{ __('form.review_btn') }}</button>
</form>
