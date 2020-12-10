@extends('site')

@section('content')
    @if ($data['category']) 
        @include('partials.title',['title' => $data['category']->getTranslatedAttribute('name'), 'isH1' => @$data['isH1']])
    @else
        @include('partials.title',['title' => 'Designer and Luxury Rooms', 'isH1' => @$data['isH1']])
    @endif

    @include('partials.breadcrumbs',['breadcrumbs' => $data['breadcrumbs']])
    
    @include('partials.filters')

    @if ($data['carousel'])
    <div class="room-carousel">
        <div class="owl-carousel owl-theme">
            @foreach($data['carousel'] as $room)
                <div class="room-item">
                    <div class="col-12">
                        <a href="{{ url_local('room', $room->slug) }}">
                            <img class="img-fluid" src="{{ asset('storage/'.str_replace('\\', '/', $room->icon)) }}"  alt="{{ $room->getTranslatedAttribute('name') }}">
                        </a>
                    </div>
                    <span><a href="{{ url_local('room', $room->slug) }}">{{ $room->getTranslatedAttribute('name') }}</a></span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div id="data-content">
        @include('product.product-items', ['products' => $data['products']])
    </div>

    @if ($data['category']) 
        <div className="category-descrition">
            <div class="text-content">{!! $data['category']->getTranslatedAttribute('description') !!}</div>
        </div>
    @endif
@endsection

@section('javascript')
    <link href="{{ asset('js/owl.carousel/owl.carousel.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/owl.carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/assets/product/product.js') }}"></script>

    @if ($data['products']->lastPage() > 1)
        <script>
            var selectedPage = 1;
            App.createPagination("{{ $data['products']->lastPage() }}", 'pag-top', "{{ \Request::get('page') ? \Request::get('page') : 1}}");
            App.createPagination("{{ $data['products']->lastPage() }}", 'pag-bottom', "{{ \Request::get('page') ? \Request::get('page') : 1}}");
        </script>
    @endif
@endsection
