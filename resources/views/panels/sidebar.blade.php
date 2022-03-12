@php
    $configData = Helper::applClasses();
@endphp
<div class="main-menu menu-fixed {{(($configData['theme'] === 'dark') || ($configData['theme'] === 'semi-dark')) ? 'menu-dark' : 'menu-light'}} menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">

            @if(Auth::user()->active_portal == 'customer' && Auth::user()->is_customer == 1 && Auth::user()->customer->activeSubscription())
                <li class="nav-item me-auto">
                    <a class="navbar-brand" href="{{route('user.home')}}">
                        <div class="brand-logo">
                            <img src="{{asset(config('app.logo'))}}" alt="{{ config('app.name') }}"/>
                        </div>
                    </a>
                </li>

            @else
                <li class="nav-item me-auto">
                    <a class="navbar-brand" href="{{route('admin.home')}}">
                        <div class="brand-logo">
                            <img src="{{asset(config('app.logo'))}}" alt="{{ config('app.name') }}"/>
                        </div>
                    </a>
                </li>
            @endif

            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pe-0" data-toggle="collapse">
                    <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                    <i class="d-none d-xl-block collapse-toggle-icon font-medium-4 text-primary" data-feather="disc" data-ticon="disc"></i>
                </a>
            </li>
        </ul>
    </div>

    <div class="shadow-bottom"></div>

    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            {{-- Foreach menu item starts --}}
            @if(isset($menuData[0]))
                @php
                    if (auth()->user()->active_portal == 'admin'){
                        $sidebarMenu = $menuData['0']->admin;
                     }else{
                        $sidebarMenu = $menuData['0']->customer;
                     }
                @endphp

                @foreach($sidebarMenu as $menu)
                    @if(isset($menu->navheader))
                        <li class="navigation-header">
                            <span>{{ $menu->navheader }}</span>
                            <i data-feather="more-horizontal"></i>
                        </li>
                    @else
                        {{-- Add Custom Class with nav-item --}}
                        @php
                            $custom_classes = "";
                            if(isset($menu->classlist)) {
                            $custom_classes = $menu->classlist;
                            }
                            $translation = "";
                            if(isset($menu->i18n)){
                            $translation = $menu->i18n;
                            }
                            $permission = explode('|', $menu->access);
                        @endphp
                        @canany($permission, auth()->user())

                            <li class="nav-item {{ isset($menu->slug) &&  str_contains(request()->path(),$menu->slug) ? 'active' : '' }} {{ $custom_classes }}">
                                <a href="{{ $menu->url }}" class="d-flex align-items-center">
                                    <i data-feather="{{ $menu->icon }}"></i>
                                    <span class="menu-title text-truncate" data-i18n="{{ $translation }}">{{ __('locale.menu.'.$menu->name) }}</span>
                                </a>
                                @if(isset($menu->submenu))
                                    @include('panels/submenu', ['menu' => $menu->submenu])
                                @endif
                            </li>
                        @endcanany
                    @endif
                @endforeach
            @endif
            {{-- Foreach menu item ends --}}
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
