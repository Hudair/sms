@if($configData["mainLayoutType"] == 'horizontal' && isset($configData["mainLayoutType"]))
    <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu {{ $configData['navbarColor'] }} navbar-fixed">
        <div class="navbar-header d-xl-block d-none">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item"><a class="navbar-brand" href="{{url('/')}}">
                        <div class="brand-logo"><img src="{{asset(config('app.logo'))}}" alt="app logo"/></div>
                    </a>
                </li>
            </ul>
        </div>
        @else
            <nav
                    class="header-navbar navbar-expand-lg navbar navbar-with-menu {{ $configData['navbarClass'] }} navbar-light navbar-shadow {{ $configData['navbarColor'] }}">
                @endif
                <div class="navbar-wrapper">
                    <div class="navbar-container content">
                        <div class="navbar-collapse" id="navbar-mobile">
                            <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                                <ul class="nav navbar-nav">
                                    <li class="nav-item mobile-menu d-xl-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs"
                                                                                          href="#"><i class="ficon feather icon-menu"></i></a></li>
                                </ul>

                            </div>
                            <ul class="nav navbar-nav float-right">
                                <li class="dropdown dropdown-language nav-item">
                                    <a class="dropdown-toggle nav-link" id="dropdown-flag" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="flag-icon flag-icon-us"></i>
                                        <span class="selected-language">English</span>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdown-flag">
                                        @foreach(\App\Helpers\Helper::languages() as $lang)
                                            <a class="dropdown-item" href="{{url('lang/'.$lang['code'])}}" data-language="{{$lang['code']}}">
                                                <i class="flag-icon flag-icon-{{$lang['iso_code']}}"></i> {{ $lang['name'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </li>
                                <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i class="ficon feather icon-maximize"></i></a></li>

                                <li class="dropdown dropdown-notification nav-item">
                                    <a class="nav-link nav-link-label" href="#" data-toggle="dropdown"><i class="ficon feather icon-bell"></i>
                                        @php
                                            $count = \App\Models\Notifications::where('user_id', Auth::user()->id)->where('mark_read', 0)->count();
                                        @endphp
                                        @if($count)
                                            <span class="badge badge-pill badge-success badge-up">{{ $count }}</span>
                                        @endif
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                                        <li class="dropdown-menu-header">
                                            <div class="dropdown-header m-0 p-2">
                                                <h3 class="white">{{ $count }} New</h3><span class="grey darken-2">App Notifications</span>
                                            </div>
                                        </li>


                                        <li class="scrollable-container media-list">

                                            @foreach(\App\Models\Notifications::where('user_id', Auth::user()->id)->where('mark_read', 0)->latest()->take('10')->cursor() as $value)
                                                <a class="d-flex justify-content-between" href="{{ route('user.account') }}">
                                                    <div class="media d-flex align-items-start">
                                                        @switch($value->notification_type)
                                                            @case('user')
                                                            <div class="media-left text-primary"><i class="feather icon-plus-square font-medium-5"></i></div>
                                                            <div class="media-body">
                                                                <h6 class="primary media-heading text-primary darken-1">You have new user</h6>
                                                                <small class="notification-text">{{ str_limit($value->message, 30) }}</small>
                                                            </div>
                                                            @break

                                                            @case('plan')
                                                            <div class="media-left text-success"><i class="feather icon-shopping-cart font-medium-5"></i></div>
                                                            <div class="media-body">
                                                                <h6 class="success media-heading text-success darken-1">You have new subscription</h6>
                                                                <small class="notification-text">{{ str_limit($value->message, 30) }}</small>
                                                            </div>
                                                            @break

                                                            @case('senderid')
                                                            <div class="media-left text-danger"><i class="feather icon-user-check font-medium-5"></i></div>
                                                            <div class="media-body">
                                                                <h6 class="danger media-heading text-danger darken-1">New Sender ID notification</h6>
                                                                <small class="notification-text">{{ str_limit($value->message, 30) }}</small>
                                                            </div>
                                                            @break

                                                            @case('number')
                                                            <div class="media-left text-info"><i class="feather icon-phone font-medium-5"></i></div>
                                                            <div class="media-body">
                                                                <h6 class="info media-heading text-info darken-1">New Number sales </h6>
                                                                <small class="notification-text">{{ str_limit($value->message, 30) }}</small>
                                                            </div>
                                                            @break

                                                            @case('keyword')
                                                            <div class="media-left text-warning"><i class="feather icon-clipboard font-medium-5"></i></div>
                                                            <div class="media-body">
                                                                <h6 class="warning media-heading text-warning darken-1">New Keyword sales</h6>
                                                                <small class="notification-text">{{ str_limit($value->message, 30) }}</small>
                                                            </div>
                                                            @break

                                                            @case('chatbox')
                                                            <div class="media-left text-danger"><i class="feather icon-message-square font-medium-5"></i></div>
                                                            <div class="media-body">
                                                                <h6 class="danger media-heading text-danger darken-1">New Inbox Message</h6>
                                                                <small class="notification-text">{{ str_limit($value->message, 30) }}</small>
                                                            </div>
                                                            @break


                                                            @case('subscription')
                                                            <div class="media-left text-danger"><i class="feather icon-shopping-cart font-medium-5"></i></div>
                                                            <div class="media-body">
                                                                <h6 class="danger media-heading text-danger darken-1">Subscription Expired!</h6>
                                                                <small class="notification-text">{{ str_limit($value->message, 60) }}</small>
                                                            </div>
                                                            @break

                                                            @case('smsunit')
                                                            <div class="media-left text-danger"><i class="feather icon-message-square font-medium-5"></i></div>
                                                            <div class="media-body">
                                                                <h6 class="danger media-heading text-danger darken-1">SMS Unit Running Low!</h6>
                                                                <small class="notification-text">{{ str_limit($value->message, 60) }}</small>
                                                            </div>
                                                            @break
                                                        @endswitch


                                                        <small>
                                                            <time class="media-meta">{{ \App\Library\Tool::formatHumanTime($value->created_at) }}</time>
                                                        </small>

                                                    </div>
                                                </a>

                                            @endforeach
                                        </li>


                                        <li class="dropdown-menu-footer">
                                            <a class="dropdown-item p-1 text-center" href="{{ route('user.account') }}">Read all notifications</a>
                                        </li>
                                    </ul>
                                </li>

                                @if(Auth::user()->active_portal == 'customer' && Auth::user()->is_customer == 1 && Auth::user()->customer->activeSubscription())
                                    <li class="nav-item balance-top-up">
                                        <div class="show-balance">
                                            <span class="show-balance-text">{{ __('locale.labels.balance') }}</span>
                                            <span class="show-balance-unit">{{ Auth::user()->sms_unit == '-1' ? __('locale.labels.unlimited') : Auth::user()->sms_unit  }}</span>
                                        </div>
                                        <a class="nav-link top-up-url" href="{{ route('user.account.top_up') }}">
                                            <button type="button" class="btn btn-sm btn-outline-success">
                                                <span class="text-white font-weight-bold" style="font-size: 12px">{{ __('locale.labels.top_up') }}</span>
                                            </button>
                                        </a>
                                    </li>
                                @endif

                                <li class="dropdown dropdown-user nav-item">
                                    <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                                        <div class="user-nav d-sm-flex d-none"><span class="user-name text-bold-600">{{Auth::user()->displayName()}}</span><span class="user-status">{{ __('locale.labels.available') }}</span></div>
                                        <span><img class="round"
                                                   src="{{ route('user.avatar', Auth::user()->uid)  }}" alt="avatar" height="40"
                                                   width="40"/></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">

                                        @if(Auth::user()->active_portal == 'admin' && Auth::user()->is_customer == 1)
                                            <a class="dropdown-item" href="{{ route('user.switch_view', ['portal' => 'customer']) }}"><i class="feather icon-log-in"></i>{{ __('locale.labels.switch_view') }}</a>
                                            <div class="dropdown-divider"></div>
                                        @endif

                                        @if(Auth::user()->active_portal == 'customer' && Auth::user()->is_admin == 1)
                                            <a class="dropdown-item" href="{{ route('user.switch_view', ['portal' => 'admin']) }}"><i class="feather icon-log-in"></i>{{ __('locale.labels.switch_view') }}</a>
                                            <div class="dropdown-divider"></div>
                                        @endif

                                        <a class="dropdown-item" href="{{ route('user.account') }}"><i class="feather icon-user"></i>{{ __('locale.labels.profile') }}</a>

                                        @if(Auth::user()->active_portal == 'customer' && Auth::user()->is_customer == 1)
                                            <a class="dropdown-item" href="{{route('customer.subscriptions.index')}}">
                                                <i class="feather icon-shopping-cart"></i>
                                                {{ __('locale.labels.billing') }}
                                            </a>
                                        @endif

                                        <div class="dropdown-divider"></div>

                                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="feather icon-power"></i> Logout</a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- END: Header-->
