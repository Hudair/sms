<!-- Chat Sidebar area -->
<div class="sidebar-content">
    <!-- Sidebar header start -->
    <div class="chat-fixed-search">
        <div class="d-flex align-items-center w-100">
            <div class="input-group input-group-merge ms-1 w-100">
                <span class="input-group-text round"><i data-feather="search" class="text-muted"></i></span>
                <input type="text" class="form-control round" id="chat-search" placeholder="{{ __('locale.labels.search') }}">
            </div>
        </div>
    </div>
    <!-- Sidebar header end -->

    <!-- Sidebar Users start -->
    <div id="users-list" class="chat-user-list-wrapper list-group">
        <ul class="chat-users-list chat-list media-list">
            @foreach($chat_box->chunk(500) as $chunk)
                @foreach($chunk as $chat)
                    <li data-id="{{$chat->uid}}">
                        <span class="avatar">
                            <img src="{{asset('images/profile/profile.jpg')}}" height="42" width="42" alt="Avatar"/>
                        </span>
                        <div class="chat-info flex-grow-1">
                            <h5 class="mb-0">{{ $chat->to }}</h5>
                            <p class="card-text text-truncate">
                                {{ $chat->from }}
                            </p>
                        </div>
                        <div class="chat-meta text-nowrap">
                            <small class="float-end mb-25 chat-time">{{ \App\Library\Tool::formatTime($chat->updated_at) }}</small>
                            @if($chat->notification)
                                <span class="badge bg-primary rounded-pill float-end notification_count">{{ $chat->notification }}</span>
                            @else
                                <div class="counter" hidden>
                                    <span class="badge bg-primary rounded-pill float-end notification_count"></span>
                                </div>
                            @endif
                        </div>
                    </li>
                @endforeach
            @endforeach

        </ul>
    </div>
    <!-- Sidebar Users end -->
</div>
<!--/ Chat Sidebar area -->
