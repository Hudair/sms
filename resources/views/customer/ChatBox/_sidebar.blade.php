@section('content-sidebar')
    <!-- Chat Sidebar area -->
    <div class="sidebar-content card">

        <div class="chat-fixed-search">
            <div class="d-flex align-items-center">
                <fieldset class="form-group position-relative has-icon-left mx-1 my-0 w-100">
                    <input type="text" class="form-control round" id="chat-search" placeholder="{{ __('locale.labels.search') }}">
                    <div class="form-control-position">
                        <i class="feather icon-search"></i>
                    </div>
                </fieldset>
            </div>
        </div>

        <div id="users-list" class="chat-user-list list-group position-relative">
            <ul class="chat-users-list-wrapper media-list">

                @foreach($chat_box->chunk(50) as $chunk)
                    @foreach($chunk as $chat)
                        <li data-id="{{$chat->uid}}">
                            <div class="pr-1">
                        <span class="avatar m-0 avatar-md"><img class="media-object rounded-circle" src="{{ asset('images/profile/profile.jpg') }}" height="42" width="42" alt="Generic placeholder image">
                        <i></i>
                        </span>
                            </div>
                            <div class="user-chat-info">
                                <div class="contact-info">
                                    <h5 class="font-weight-bold mb-0">{{ \App\Helpers\Helper::contactName($chat->to) }}</h5>
                                    <p class="truncate">{{ $chat->from }}</p>
                                </div>
                                <div class="contact-meta">
                                    <span class="float-right mb-25">{{ \App\Library\Tool::formatTime($chat->updated_at) }}</span>
                                    @if($chat->notification)
                                        <span class="badge badge-primary badge-pill float-right">{{ $chat->notification }}</span>
                                    @endif

                                </div>
                            </div>
                        </li>
                    @endforeach
                @endforeach
            </ul>
        </div>
    </div>
    <!--/ Chat Sidebar area -->

@endsection
