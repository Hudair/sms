@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Plugins'))

@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('css/pages/checkout.css')) }}">
@endsection

@section('content')

    <!-- Ecommerce Products Starts -->
    <section id="ecommerce-products" class="grid-view">
        <div class="card ecommerce-card">
            <div class="card-content">
                <a href="#">
                    <div class="item-img text-center">
                        <img class="img-fluid" src="{{ asset('images/pages/shop/4.png') }}" alt="img-placeholder">
                    </div>
                    <div class="card-body">
                        <div class="item-wrapper">
                            <div class="item-rating">
                                <div class="badge badge-primary badge-md">
                                    <span>Coming Soon</span>
                                </div>
                            </div>
                            <div>
                                <h6 class="item-price">
                                    $39.00
                                </h6>
                            </div>
                        </div>
                        <div class="item-name">
                            <span>uWhiteLabel - White Label Reseller Management system</span>
                            <p class="item-company">By <span class="company-name">Codeglen</span></p>
                        </div>
                        <div>
                            <p class="item-description">
                                uWhiteLabel is build for ultimate sms White Label Reseller Management system.
                            </p>
                        </div>
                    </div>
                </a>
                <div class="item-options text-center">
                    <div class="wishlist">
                        <i class="feather icon-layout"></i> <span>Preview</span>
                    </div>
                    <div class="cart">
                        <i class="feather icon-shopping-cart"></i>
                        <span class="add-to-cart">Buy a copy</span> <a href="#" class="view-in-cart d-none">View In Cart</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card ecommerce-card">
            <div class="card-content">
                <a href="#">
                    <div class="item-img text-center">
                        <img class="img-fluid" src="{{ asset('images/pages/shop/4.png') }}" alt="img-placeholder">
                    </div>
                    <div class="card-body">
                        <div class="item-wrapper">
                            <div class="item-rating">
                                <div class="badge badge-primary badge-md">
                                    <span>Coming Soon</span>
                                </div>
                            </div>
                            <div>
                                <h6 class="item-price">
                                    $39.00
                                </h6>
                            </div>
                        </div>
                        <div class="item-name">
                            <span>uFlowBuilder - Flow Builder Addons for Ultimate SMS</span>
                            <p class="item-company">By <span class="company-name">Codeglen</span></p>
                        </div>
                        <div>
                            <p class="item-description">
                                uFlowBuilder allows you to create more meaningful customer interactions on the platforms they fluently use.
                            </p>
                        </div>
                    </div>
                </a>
                <div class="item-options text-center">
                    <div class="wishlist">
                        <i class="feather icon-layout"></i> <span>Preview</span>
                    </div>
                    <div class="cart">
                        <i class="feather icon-shopping-cart"></i>
                        <span class="add-to-cart">Buy a copy</span> <a href="#" class="view-in-cart d-none">View In Cart</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card ecommerce-card">
            <div class="card-content">
                <a href="#">
                    <div class="item-img text-center">
                        <img class="img-fluid" src="{{ asset('images/pages/shop/4.png') }}" alt="img-placeholder">
                    </div>
                    <div class="card-body">
                        <div class="item-wrapper">
                            <div class="item-rating">
                                <div class="badge badge-primary badge-md">
                                    <span>Coming Soon</span>
                                </div>
                            </div>
                            <div>
                                <h6 class="item-price">
                                    $59.00
                                </h6>
                            </div>
                        </div>
                        <div class="item-name">
                            <span>uAppLanding - App Landing Page for Ultimate SMS</span>
                            <p class="item-company">By <span class="company-name">Codeglen</span></p>
                        </div>
                        <div>
                            <p class="item-description">
                                uAppLanding is a clean and modular App Landing Page for Ultimate SMS.
                            </p>
                        </div>
                    </div>
                </a>
                <div class="item-options text-center">
                    <div class="wishlist">
                        <i class="feather icon-layout"></i> <span>Preview</span>
                    </div>
                    <div class="cart">
                        <i class="feather icon-shopping-cart"></i>
                        <span class="add-to-cart">Buy a copy</span> <a href="#" class="view-in-cart d-none">View In Cart</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Ecommerce Products Ends -->

@endsection


