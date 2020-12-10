<ul class="menu-level-2">
    @foreach ($items as $key => $item)
        <li><a href="{{ url_local('brand', $key)}}">{{ $item }}</a></li>
    @endforeach
    <li class="no-dots"><a href="{{ url_local('brands') }}" class=view-all-menu>View all Brands</a></li>
</ul>

