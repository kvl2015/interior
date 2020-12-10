@extends('site')

@section('content')
    <div class="cart-checkout">
        @include('partials.title',['title' => __('Shopping Cart')])

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url_local('/') }}">{{ __('page.page_main') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('order.page_title') }}</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-6"><a href="{{ url_local('/') }}" class="btn btn-continue">{{ __('cart.continue_shopping') }}</a></div>
            <div class="col-lg-6"><div class="btn-checkout-container"><a href="{{ url_local('/checkout') }}" class="btn btn-order">{{ __('cart.secure_checkout') }}</a></div></div>
        </div>
        <div class="row checkout-data">
            @include('cart.items', ['cart' => $cart])
        </div>
        <div class="row discount-total">
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="control-label">Enter your discount voucher / code</label>
                    <input type="text" class="form-control" value="" id="discount-code" name="discount-code" placeholder="Enter your discount voucher / code">
                    <a href="javascript:;" onClick="getCupone($('#discount-code').val())" class="btn btn-apply">Apply</a>
                </div>
            </div>
            <div class="col-lg-6 position-relative">
                <div class="amounts">  
                    <dl>
                        <dt>Order Summary:</dt>
                        <dd class="sc-amount-summary">€{{ $amounts['summary'] }}</dd>
                    </dl> 
                </div>
            </div>
        </div>
        <div class="row secure-shopping-container">
            <div class="col-lg-6">
                <p>Secure Shopping:</p>
                <ul class="secure-list">
                    <li><i class="fas fa-check"></i>Only original products</li>
                    <li><i class="fas fa-check"></i>Reliable shipping</li>
                    <li><i class="fas fa-check"></i>Easy return</li>
                    <li><i class="fas fa-check"></i>Money back guaranty</li>
                    <li><i class="fas fa-check"></i>"https" secured pages</li>
                    <li><i class="fas fa-check"></i>Secure payment</li>
                    <li><i class="fas fa-check"></i>Secure data protection</li>
                    <li><i class="fas fa-check"></i>3-D Secure programs</li>
                </ul>    
                <br/>
                <div class="d-flex justify-content-center payment-secure">
                    <img src="/images/cart-pp.jpg" height="35" />
                    <img src="/images/cart-visa.png" height="35" />
                    <img src="/images/cart-master.png" height="35" />
                    <img src="/images/cart-safe.png" height="35" />
                </div>
            </div>
            <div class="col-lg-6 position-relative">
                <div class="amounts">
                    <table width="100%">
                        <tr>
                            <td width="50%"><span class="">Subtotal:</span></td>
                            <td width="50%" align="right" class="sc-amount-subtotal">€{{ $amounts['summary'] }}</td>
                        </tr>
                        <tr>
                            <td width="50%"><span class="shipping">Shipping cost to:</span></td>
                            <td width="50%" align="right" class="shipping sc-amount-shipping">€{{ $amounts['shipping'] }}</td>
                        </tr>
                        <tr class="discount-row hide">
                            <td width="50%"><span class="">Discount:</span></td>
                            <td width="50%" align="right" class="sc-amount-discount">€{{ $amounts['discount'] }}</td>
                        </tr>
                        <tr>
                            <td width="50%"><span class="">Grand Total:</span></td>
                            <td width="50%" align="right" class="sc-amount-grand-total">€{{ $amounts['grand'] }}</td>
                        </tr>
                    </table>
                    
                </div>
                <div class="clearfix"></div>
                <div class="align-left">
                    <a href="/page/vat"><span>incl.VAT</span></a> 
                    <div>3% discount on prepayment via advance bank transfer (you can select the payment method on the checkout)</div>
                    <div>To prevent unauthorised access to your data, the order and payment process is encrypted, 
                        by hybrid encryption protocol for the secure data transmission "Secure Socket Layer" (SSL) with 256 bit.
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="btn-checkout-container"><a href="{{ url_local('/checkout') }}" class="btn btn-order">To secure checkout</a></div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
@endsection