<div class="modal-container-header">Contact information</div>
<form method="POST" class="request-product-form" id="send-product-request-form" onsubmit="return false">
    @csrf

    @include('form.fields.text-input',['name' => 'first_name', 'label' => 'First name', 'value' => '', 'type' => 'text', 'required' => true])

    @include('form.fields.text-input',['name' => 'last_name', 'label' => 'Last name', 'value' => '', 'type' => 'text', 'required' => true])

    @include('form.fields.text-input',['name' => 'delivery_to', 'label' => 'Delivery to', 'value' => '', 'type' => 'text', 'required' => true])

    @include('form.fields.text-input',['name' => 'company', 'label' => 'Company', 'value' => '', 'type' => 'text', 'required' => true])

    @include('form.fields.text-input',['name' => 'email', 'label' => 'Email', 'value' => '', 'type' => 'text', 'required' => true])

    @include('form.fields.text-input',['name' => 'phone', 'label' => 'Telephone', 'value' => '', 'type' => 'text', 'required' => false])

    @include('form.fields.textarea',['name' => 'comment', 'label' => 'Comment', 'value' => '', 'required' => false])


    <input type="hidden" name="product_id" value="{{ $product_id }}" />
    <input type="hidden" name="rate" value="" />

    <button type="submit" class="btn btn-info hvr-sweep-to-top" id="sendReview" value="Email Enquiry">Email Enquiry</button>
</form>
