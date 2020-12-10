@extends('site')

@section('content')
    @include('partials.title',['title' => 'Visited products', 'isH1' => @$data['isH1']])

    @include('partials.breadcrumbs',['breadcrumbs' => $data['breadcrumbs']])
    
    <div id="data-content">
        @include('product.product-items', ['products' => $data['products']])
    </div>

@endsection

@if ($data['products']->lastPage() > 1)
    @section('javascript')
        <script src="{{ asset('js/assets/product/product.js') }}"></script>
        <script>
            var selectedPage = 1;
            App.createPagination("{{ $data['products']->lastPage() }}", 'pag-top', "{{ \Request::get('page') ? \Request::get('page') : 1}}");
            App.createPagination("{{ $data['products']->lastPage() }}", 'pag-bottom', "{{ \Request::get('page') ? \Request::get('page') : 1}}");
        </script>
    @endsection
@endif
