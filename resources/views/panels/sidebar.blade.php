@php
    $configData = Helper::applClasses();
@endphp
<div
        class="main-menu menu-fixed {{($configData['theme'] === 'light') ? "menu-light" : "menu-dark"}} menu-accordion menu-shadow"
        data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto">
                <a class="navbar-brand" href="{{route('admin.home')}}">
                    <div class="brand-logo">
                        <img src="{{asset(config('app.logo'))}}" alt="app logo"/>
                    </div>
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                    <i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i>
                    <i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block primary collapse-toggle-icon"
                       data-ticon="icon-disc"></i>
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
                                <a href="{{ $menu->url }}">
                                    <i class="{{ $menu->icon }}"></i>
                                    <span class="menu-title"
                                          data-i18n="{{ $translation }}">{{ __('locale.menu.'.$menu->name) }}</span>
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
