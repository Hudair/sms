{{-- For submenu --}}
<ul class="menu-content">
    @if(isset($menu))
        @foreach($menu as $submenu)
            @php
                $submenuTranslation = "";
                if (isset($menu->i18n)) {
                    $submenuTranslation = $menu->i18n;
                }
            @endphp

            @can($submenu->access, auth()->user())
                <li class="{{ isset($submenu->slug) && str_contains(request()->path(),$submenu->slug) ? 'active' : '' }}">
                    <a href="{{isset($submenu->url) ? url($submenu->url):'javascript:void(0)'}}" class="d-flex align-items-center">
                        @if(isset($submenu->icon))
                            <i data-feather="{{ $submenu->icon ?? "" }}"></i>
                        @endif
                        <span class="menu-item text-truncate" data-i18n="{{ $submenuTranslation }}">{{ __('locale.menu.'.$submenu->name) }}</span>
                    </a>
                    @if (isset($submenu->submenu))
                        @include('panels/submenu', ['menu' => $submenu->submenu])
                    @endif
                </li>
            @endcan
        @endforeach
    @endif
</ul>
