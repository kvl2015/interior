<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-bars"></i>
    </button>
<nav class="navbar navbar-expand-lg">
    <div class="scrolled-content">
    <div class="navbar-collapse collapse" id="navbarNavDropdown">
        <ul class="navbar-nav category-menu">
            @foreach($items as $item)
                @if ( $item['parent'][0] != 'mirrors')
                    <li class="nav-item dropdown position-static">
                        <a href="{{ url_local($item['parent'][0]) }}" class="nav-link"><span>{{ $item['parent'][1] }}</span></a>
                        <i class="fas fa-chevron-down shevron-1"></i>
                        <div class="clearfix"></div>
                        @if (isset($item['childs']))
                            <div class="dropdown-menu dropdown-menu-full" role="menu">
                                <div class="container shadowed">
                                    <div class="row w-100">
                                        @foreach($item['childs'] as $child)
                                            @if (isset($child['childs']))
                                            <div class="col-sm">
                                                <span class="menu-level-1"><a href="{{ url_local($child['parent'][0]) }}">{{ $child['parent'][1] }}</a></span>
                                                <i class="fas fa-chevron-down shevron-2"></i>
                                                <ul class="menu-level-2">
                                                    @foreach($child['childs'] as $_child)
                                                        <li><a href="{{ url_local($_child['parent'][0]) }}">{{ $_child['parent'][1] }}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a href="{{ url_local($item['parent'][0]) }}" class="nav-link"><span>{{$item['parent'][1]}}</span></a>
                        <i class="fas fa-chevron-down shevron-1"></i>
                        <ul class="dropdown-menu level-single" role="menu">
                            @php //dd($item['childs']) @endphp
                            @foreach($item['childs'] as $child)
                                <li><a href="{{ url_local($child['parent'][0]) }}">{{ $child['parent'][1] }}</a></li>    
                            @endforeach
                        </ul>                        
                    </li>
                @endif
            @endforeach
            @include('menu.shopby')
        </ul>
    </div>
    </div>
</nav>
