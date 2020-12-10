<ul class="menu-level-2">
    @foreach ($items as $key => $item)
        <li><a href="{{ url_local('room', $key)}}">{{ $item }}</a></li>
    @endforeach
    <li class="no-dots">
        <a href="{{ url_local('room') }}" class=view-all-menu>View all Rooms</a>
    </li>
</ul>


