@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Theme Customizer'))

@section('content')
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row">

            {{-- BEGIN: Customizer --}}
            <div class="customizer d-none d-md-block">

                <a class="customizer-toggle d-flex align-items-center justify-content-center" href="javascript:void(0);">
                    <i class="spinner" data-feather="settings"></i>
                </a>

                <div class="customizer-content">
                    <!-- Customizer header -->
                    <div class="customizer-header px-2 pt-1 pb-0 position-relative">
                        <h4 class="mb-0">Theme Customizer</h4>
                        <p class="m-0">Customize & Preview in Real Time</p>

                        <a class="customizer-close" href="javascript:void(0);"><i data-feather="x"></i></a>
                    </div>

                    <hr/>

                    <!-- Styling & Text Direction -->
                    <div class="customizer-styling-direction px-2">
                        <p class="fw-bold">Skin</p>
                        <div class="d-flex">
                            <div class="form-check me-1">
                                <input type="radio" id="skinlight" name="skinradio" class="form-check-input layout-name" checked="" data-layout="">
                                <label class="form-check-label" for="skinlight">Light</label>
                            </div>
                            <div class="form-check me-1">
                                <input type="radio" id="skinbordered" name="skinradio" class="form-check-input layout-name" data-layout="bordered-layout">
                                <label class="form-check-label" for="skinbordered">Bordered</label>
                            </div>
                            <div class="form-check me-1">
                                <input type="radio" id="skindark" name="skinradio" class="form-check-input layout-name" data-layout="dark-layout">
                                <label class="form-check-label" for="skindark">Dark</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" id="skinsemidark" name="skinradio" class="form-check-input layout-name" data-layout="semi-dark-layout">
                                <label class="form-check-label" for="skinsemidark">Semi Dark</label>
                            </div>
                        </div>
                    </div>

                    <hr/>

                    <!-- Menu -->
                    <div class="customizer-menu px-2">
                        <div id="customizer-menu-collapsible" class="d-flex">
                            <p class="fw-bold me-auto m-0">Menu Collapsed</p>
                            <div class="form-check form-check-primary form-switch">
                                <input type="checkbox" class="form-check-input" id="collapse-sidebar-switch">
                                <label class="form-check-label" for="collapse-sidebar-switch"></label>
                            </div>
                        </div>
                    </div>
                    <hr/>

                    <!-- Layout Width -->
                    <div class="customizer-footer px-2">
                        <p class="fw-bold">Layout Width</p>
                        <div class="d-flex">
                            <div class="form-check me-1">
                                <input type="radio" id="layout-width-full" name="layoutWidth" class="form-check-input" checked="">
                                <label class="form-check-label" for="layout-width-full">Full Width</label>
                            </div>
                            <div class="form-check me-1">
                                <input type="radio" id="layout-width-boxed" name="layoutWidth" class="form-check-input">
                                <label class="form-check-label" for="layout-width-boxed">Boxed</label>
                            </div>
                        </div>
                    </div>
                    <hr/>

                    <!-- Navbar -->
                    <div class="customizer-navbar px-2">
                        <div id="customizer-navbar-colors">
                            <p class="fw-bold">Navbar Color</p>
                            <ul class="list-inline unstyled-list">
                                <li class="color-box bg-white border selected" data-navbar-default=""></li>
                                <li class="color-box bg-primary" data-navbar-color="bg-primary"></li>
                                <li class="color-box bg-secondary" data-navbar-color="bg-secondary"></li>
                                <li class="color-box bg-success" data-navbar-color="bg-success"></li>
                                <li class="color-box bg-danger" data-navbar-color="bg-danger"></li>
                                <li class="color-box bg-info" data-navbar-color="bg-info"></li>
                                <li class="color-box bg-warning" data-navbar-color="bg-warning"></li>
                                <li class="color-box bg-dark" data-navbar-color="bg-dark"></li>
                            </ul>
                        </div>

                        <p class="navbar-type-text fw-bold">Navbar Type</p>
                        <div class="d-flex">
                            <div class="form-check me-1">
                                <input type="radio" id="nav-type-floating" name="navType" class="form-check-input" checked="">
                                <label class="form-check-label" for="nav-type-floating">Floating</label>
                            </div>
                            <div class="form-check me-1">
                                <input type="radio" id="nav-type-sticky" name="navType" class="form-check-input">
                                <label class="form-check-label" for="nav-type-sticky">Sticky</label>
                            </div>
                            <div class="form-check me-1">
                                <input type="radio" id="nav-type-static" name="navType" class="form-check-input">
                                <label class="form-check-label" for="nav-type-static">Static</label>
                            </div>
                        </div>
                    </div>
                    <hr/>

                    <!-- Footer -->
                    <div class="customizer-footer px-2">
                        <p class="fw-bold">Footer Type</p>
                        <div class="d-flex">
                            <div class="form-check me-1">
                                <input type="radio" id="footer-type-sticky" name="footerType" class="form-check-input">
                                <label class="form-check-label" for="footer-type-sticky">Sticky</label>
                            </div>
                            <div class="form-check me-1">
                                <input type="radio" id="footer-type-static" name="footerType" class="form-check-input" checked="">
                                <label class="form-check-label" for="footer-type-static">Static</label>
                            </div>
                            <div class="form-check me-1">
                                <input type="radio" id="footer-type-hidden" name="footerType" class="form-check-input">
                                <label class="form-check-label" for="footer-type-hidden">Hidden</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- End: Customizer --}}
        </div>

        <div class="row match-height">
            <div class="col-md-6 col-12">

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('locale.menu.Theme Customizer') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical" action="{{ route('admin.theme.customizer') }}" method="post">
                                @csrf


                                <div class="col-12">
                                    <div class="mb-1">
                                        <label for="mainLayoutType" class="form-label required">Menu Layout</label>
                                        <select class="form-select" id="mainLayoutType" name="mainLayoutType">
                                            <option value="vertical" {{ env('THEME_LAYOUT_TYPE') == 'vertical' ? 'selected': null }}>Vertical</option>
                                            <option value="horizontal" {{ env('THEME_LAYOUT_TYPE') == 'horizontal' ? 'selected': null }}>Horizontal</option>
                                        </select>
                                    </div>
                                    @error('mainLayoutType')
                                    <p><small class="text-danger">{{ $message }}</small></p>
                                    @enderror
                                </div>


                                <div class="col-12">
                                    <div class="mb-1">
                                        <label for="theme" class="form-label required">Skin</label>
                                        <select class="form-select" id="theme" name="theme">
                                            <option value="light" {{ config('custom.horizontal.theme') == 'light' ? 'selected': null }}>Light</option>
                                            <option value="bordered" {{ config('custom.horizontal.theme') == 'bordered' ? 'selected': null }}>Bordered</option>
                                            <option value="dark" {{ config('custom.horizontal.theme') == 'dark' ? 'selected': null }}>Dark</option>
                                            <option value="semi-dark" {{ config('custom.horizontal.theme') == 'semi-dark' ? 'selected': null }}>Semi Dark</option>
                                        </select>
                                    </div>
                                    @error('theme')
                                    <p><small class="text-danger">{{ $message }}</small></p>
                                    @enderror
                                </div>


                                <div class="col-12">
                                    <div class="mb-1">
                                        <label for="navbarColor" class="form-label required">Navbar Color</label>
                                        <select class="form-select" id="navbarColor" name="navbarColor">
                                            <option value="bg-primary" {{ config('custom.horizontal.navbarColor') == 'bg-primary' ? 'selected': null }}>Purple</option>
                                            <option value="bg-info" {{ config('custom.horizontal.navbarColor') == 'bg-info' ? 'selected': null }}>Blue</option>
                                            <option value="bg-warning" {{ config('custom.horizontal.navbarColor') == 'bg-warning' ? 'selected': null }}>Orange</option>
                                            <option value="bg-success" {{ config('custom.horizontal.navbarColor') == 'bg-success' ? 'selected': null }}>Green</option>
                                            <option value="bg-danger" {{ config('custom.horizontal.navbarColor') == 'bg-danger' ? 'selected': null }}>Red</option>
                                            <option value="bg-dark" {{ config('custom.horizontal.navbarColor') == 'bg-dark' ? 'selected': null }}>Dark</option>
                                            <option value="bg-white" {{ config('custom.horizontal.navbarColor') == 'bg-white' ? 'selected': null }}>White</option>
                                            <option value="bg-secondary" {{ config('custom.horizontal.navbarColor') == 'bg-secondary' ? 'selected': null }}>Gray</option>
                                        </select>
                                    </div>
                                    @error('navbarColor')
                                    <p><small class="text-danger">{{ $message }}</small></p>
                                    @enderror
                                </div>


                                <div class="col-12">
                                    <div class="mb-1">
                                        <label for="navbarType" class="form-label required">Navbar Type</label>
                                        <select class="form-select" id="navbarType" name="navbarType">
                                            <option value="floating" {{ config('custom.horizontal.horizontalMenuType') == 'floating' ? 'selected': null }}>Floating</option>
                                            <option value="static" {{ config('custom.horizontal.horizontalMenuType') == 'static' ? 'selected': null }}>Static</option>
                                            <option value="sticky" {{ config('custom.horizontal.horizontalMenuType') == 'sticky' ? 'selected': null }}>Sticky</option>
                                        </select>
                                    </div>
                                    @error('navbarType')
                                    <p><small class="text-danger">{{ $message }}</small></p>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <div class="mb-1">
                                        <label for="footerType" class="form-label required">Footer Type</label>
                                        <select class="form-select" id="footerType" name="footerType">
                                            <option value="static" {{ config('custom.horizontal.footerType') == 'static' ? 'selected': null }}>Static</option>
                                            <option value="sticky" {{ config('custom.horizontal.footerType') == 'sticky' ? 'selected': null }}>Sticky</option>
                                            <option value="hidden" {{ config('custom.horizontal.footerType') == 'hidden' ? 'selected': null }}>Hidden</option>
                                        </select>
                                    </div>
                                    @error('footerType')
                                    <p><small class="text-danger">{{ $message }}</small></p>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <div class="mb-1">
                                        <label for="layoutWidth" class="form-label required">Layout Width</label>
                                        <select class="form-select" id="layoutWidth" name="layoutWidth">
                                            <option value="full" {{ config('custom.horizontal.layoutWidth') == 'full' ? 'selected': null }}>Full Width</option>
                                            <option value="boxed" {{ config('custom.horizontal.layoutWidth') == 'boxed' ? 'selected': null }}>Boxed</option>
                                        </select>
                                    </div>
                                    @error('layoutWidth')
                                    <p><small class="text-danger">{{ $message }}</small></p>
                                    @enderror
                                </div>


                                <div class="col-12">
                                    <div class="mb-1">
                                        <label for="sidebarCollapsed" class="form-check-label">Menu Collapsed</label>
                                        <div class="form-switch me-3 me-lg-5 mt-1">
                                            <input type="checkbox" class="form-check-input" id="sidebarCollapsed" @if(config('custom.horizontal.sidebarCollapsed')) checked @endif value="true" name="sidebarCollapsed">
                                        </div>
                                        <p><small class="text-danger">Warning:this option only applies to the vertical layout</small></p>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-1">
                                        <label for="pageHeader" class="form-check-label">Show Breadcrumbs</label>
                                        <div class="form-switch me-3 me-lg-5 mt-1">
                                            <input type="checkbox" class="form-check-input" id="pageHeader" @if(config('custom.horizontal.pageHeader') == true) checked @endif  value="true" name="pageHeader">
                                        </div>
                                    </div>
                                </div>


                                <div class="col-12 mt-2">
                                    <button type="submit" class="btn btn-primary mb-1">
                                        <i data-feather="save"></i> {{__('locale.buttons.save')}}
                                    </button>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </div>


        </div>
    </section>
    <!-- // Basic Vertical form layout section end -->


@endsection


@section('page-script')

    <script src="{{ asset(mix('js/scripts/customizer.js')) }}"></script>

    <script>

        let showCustom = $('.show-custom'),
            NavbarColor = $('#navbarColor'),
            firstInvalid = $('form').find('.is-invalid').eq(0);

        if (firstInvalid.length) {
            $('body, html').stop(true, true).animate({
                'scrollTop': firstInvalid.offset().top - 200 + 'px'
            }, 200);
        }


        if (NavbarColor.val() === 'custom') {
            showCustom.show();
        } else {
            showCustom.hide();
        }

        NavbarColor.on('change', function () {
            if (NavbarColor.val() === 'custom') {
                showCustom.show();
            } else {
                showCustom.hide();
            }

        });

    </script>
@endsection
