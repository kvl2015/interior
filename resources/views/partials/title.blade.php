@if (isset($isH1))
    @if ($isH1)
        <div class="page-title"><h1>{{$title}}</h1></div>
    @else
        <div class="page-title"><span>{{$title}}</span></div>
    @endif
@else
    <div class="page-title"><span>{{$title}}</span></div>
@endif
