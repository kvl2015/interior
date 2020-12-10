<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url_local('/') }}">{{ __('page.page_main') }}</a></li>
        @foreach($breadcrumbs as $key => $link)
            @php $data = explode('@', $link) @endphp
            @if ($key >= count($breadcrumbs) -1)
                <li class="breadcrumb-item active" aria-current="page">{!! $data[1] !!}</li>
            @else
                <li class="breadcrumb-item active"><a href="{{ url_local($data[0]) }}">{!! $data[1] !!}</a></li>
            @endif
        @endforeach
    </ol>
</nav>
