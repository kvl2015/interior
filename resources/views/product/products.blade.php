@extends('site')

@section('content')
    @include('partials.title',['title' => $data['category']->getTranslatedAttribute('name'), 'isH1' => @$data['isH1']])

    @include('partials.breadcrumbs',['breadcrumbs' => $data['breadcrumbs']])
    
    @if (isset($data['category']->childs))
        <div class="row childs-container">
                @foreach ($data['category']->childs as $child)
                    @if ($child->image)
                    <div class="child-grid-col col-6">
                        <a href="{{ url_local($child->slug) }}" title="{{ $child->getTranslatedAttribute('name') }}">
                            <img class="img-fluid lazy" 
                                src="{{ asset('storage/'.$child->image) }}"
                                data-src="{{ asset('storage/'.$child->image) }}"  
                                alt="{{ $child->getTranslatedAttribute('name') }}" 
                                title="{{ $child->getTranslatedAttribute('name') }}" />
                        
                        <div class="caption">{{ $child->getTranslatedAttribute('name') }}</div>
                        </a>
                    </div>
                    @endif
                @endforeach
        </div>
    @endif
    
    @include('partials.filters')

    <div id="data-content">
        @include('product.product-items', ['products' => $data['products']])
    </div>

    <div className="category-descrition">
        <div class="text-content">{!! $data['category']->getTranslatedAttribute('description') !!}</div>
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
