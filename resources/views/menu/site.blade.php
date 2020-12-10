<b>{{ $items['name']}}</b><br/>
<ul class="navbar-nav">
    @foreach ($items['links'] as $link)
        <li class="nav-item"><a href="{{ url_local('page', $link->slug) }}" class="nav-link">{{ $link->getTranslatedAttribute('title') }}</a></li>
    @endforeach
</ul>