@extends('site')

@section('content')
    <div class='row'>
        <div class="col-lg-12">
            @if ($data['slug'] == 'contact')
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        @include('partials.title',['title' => $data['page']->getTranslatedAttribute('title')])

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url_local('/') }}">{{ __('page.page_main') }}&#128293;</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{  $data['page']->getTranslatedAttribute('title') }}</li>
                            </ol>
                        </nav>

                        <div class="text-content">
                            {!! $data['page']->getTranslatedAttribute('body') !!}
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        @include('partials.title',['title' => __('page.form_contact')])

                        @include('forms.contact')
                    </div>

                </div>
            @else
                @include('partials.title',['title' => $data['page']->getTranslatedAttribute('title')])

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url(App::getLocale() == 'ru' ? 'ru/' : '/') }}">{{ __('page.page_main') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{  $data['page']->getTranslatedAttribute('title') }}</li>
                    </ol>
                </nav>

                <div class="text-content">
                    {!! $data['page']->getTranslatedAttribute('body') !!}
                </div>
            @endif
        </div>
    </div>
@endsection

