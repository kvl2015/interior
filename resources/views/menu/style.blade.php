<ul class="menu-level-2">
    @foreach ($items as $key => $item)
        <li><a href="{{ url_local('style', $key)}}">{{ $item }}</a></li>
    @endforeach
</ul>

