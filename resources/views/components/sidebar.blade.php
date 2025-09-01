<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('dashboard') }}" class="sidebar-logo">

                        @if(config('app.client_name') == "Alkarim")
                     <img src="{{ asset('assets/images/alkarim.png') }}" alt="site logo" class="light-logo">
                    <img src="{{ asset('assets/images/alkarim-light.png') }}" alt="site logo" class="dark-logo">
                    <img src="{{ asset('assets/images/alkarim-icon.png') }}" alt="site logo" class="logo-icon">
               @elseif(config('app.client_name') == "Upedia")
                    <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo">
                    <img src="{{ asset('assets/images/logo-light.png') }}" alt="site logo" class="dark-logo">
                    <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="logo-icon">
               @else
                 <img src="{{ asset('assets/images/tailors.png') }}" alt="site logo" class="light-logo">
                <img src="{{ asset('assets/images/tailors-light.png') }}" alt="site logo" class="dark-logo">
               <img src="{{ asset('assets/images/tailors-icon.png') }}" alt="site logo" class="logo-icon">
            @endif
            
        </a>
    </div>
    <div class="sidebar-menu-area">
          
<ul class="sidebar-menu">
    
    @foreach(session('menuTabs') as $tab)
           @if($tab->children->count() > 0)
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="{{ $tab->icon }}" class="menu-icon"></iconify-icon>
                    <span>{{ $tab->label }}</span>
                </a>
                    <ul class="sidebar-submenu">
                        @foreach($tab->children as $child)
                      
                            @if(!$child->permission_required || auth()->user()->can($child->permission_required))
                                <li>
                                    <a href="{{ $child->url }}">
                                        <iconify-icon icon="{{ $child->icon }}" class="me-2"></iconify-icon>
                                        <span>{{ $child->label }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
            </li>
           @elseif($tab->parent_id)
    
        @else
       
            <li>
                <a href="{{ $tab->url }}">
                    <iconify-icon icon="{{ $tab->icon }}" class="menu-icon"></iconify-icon>
                    <span>{{ $tab->label }}</span>
                </a>
            </li>
        @endif

    @endforeach
</ul>
    </div>
</aside>
