<ul class="menu-level-2">
    @foreach ($items as $key => $item)
        <li><a href="{{ url_local('designer', $key)}}">{{ $item }}</a></li>
    @endforeach
    <li class="no-dots"><a href="{{ url_local('designers') }}" class=view-all-menu>View all Designers</a></li>
</ul>

