<li class="nav-item dropdown position-static">
    <a href="" class="nav-link"><span>Shop by</span></a>
    <i class="fas fa-chevron-down shevron-1"></i>
    <div class="dropdown-menu dropdown-menu-full" role="menu">
        <div class="container py-3 shadowed">
            <div class="row w-100">
                <div class="col-sm">
                    <span class="menu-level-1"><a href="javascript:;">Rooms</a></span>
                    <i class="fas fa-chevron-down shevron-2"></i>
                    {{ room_menu() }}
                </div>
                <div class="col-sm">
                    <span class="menu-level-1"><a href="javascript:;">Designers</a></span>
                    <i class="fas fa-chevron-down shevron-2"></i>
                    {{ designer_menu() }}
                </div>
                <div class="col-sm">
                    <span class="menu-level-1"><a href="javascript:;">Brands</a></span>
                    <i class="fas fa-chevron-down shevron-2"></i>
                    {{ brand_menu() }}
                </div>
                <div class="col-sm">
                    <span class="menu-level-1"><a href="javascript:;">Style</a></span>
                    <i class="fas fa-chevron-down shevron-2"></i>
                    {{ style_menu() }}
                </div>
                <div class="col-sm">
                    <span class="menu-level-1"><a href="javascript:;">In stock</a></span>
                    <i class="fas fa-chevron-down shevron-2"></i>
                    <ul class="menu-level-2">
                        <li><a href="#">New condition</a></li>
                        <li><a href="#">Sample sale</a></li>
                        <li class="no-dots"><a href="#" class=view-all-menu>View all Sale</a></li>
                    </ul>
                    
                </div>            
            </div>
        </div>
    </div>
</li>
