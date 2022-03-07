@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Chat Box'))

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/pages/app-chat.css')) }}">
@endsection
@include('customer.ChatBox._sidebar')
@section('content')
    <div class="chat-overlay"></div>
    <section class="chat-app-window">
        <div class="start-chat-area">
            <span class="mb-1 start-chat-icon feather sidebar-toggle icon-message-square"></span>
            <h4 class="py-50 px-1 start-chat-text">
                <a href="{{ route('customer.chatbox.new') }}" class="text-dark">{{ __('locale.labels.new_conversion') }}</a>
            </h4>
        </div>

        <div class="active-chat d-none">
            <div class="chat_navbar">
                <header class="chat_header d-flex justify-content-between align-items-center p-1 mb-md-3">
                    <div class="vs-con-items d-flex align-items-center">
                        <div class="sidebar-toggle d-block d-lg-none mr-1"><i class="feather icon-menu font-large-1"></i></div>
                    </div>
                    <span class="d-md-none">
                        <a href="{{ route('customer.chatbox.new') }}" class="text-dark"><i class="feather icon-plus font-medium-5"></i></a>
                    </span>
                </header>
            </div>
            <div class="user-chats">
                <div class="chats">
                    <div class="chat_history"></div>
                </div>
            </div>
            <div class="chat-app-form">
                <form class="chat-app-input d-flex" onsubmit="enter_chat();" action="javascript:void(0);">
                    <input type="text" class="form-control message mr-1 ml-50" id="iconLeft4-1" placeholder="{{ __('locale.campaigns.type_your_message') }}">
                    <button type="button" class="btn btn-primary btn-sm send mr-1" onclick="enter_chat();"><i class="fa fa-paper-plane-o d-lg-none"></i> <span class="d-none d-lg-block">{{ __('locale.buttons.send') }}</span></button>
                </form>
            </div>
        </div>


    </section>
@endsection

@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/scripts/pages/chat.js')) }}"></script>

    <script>

        // autoscroll to bottom of Chat area
        let chatContainer = $(".user-chats"),
            details,
            chatHistory = $(".chat_history");
        $(".chat-users-list-wrapper li").on("click", function () {

            chatHistory.empty();
            chatContainer.animate({scrollTop: chatContainer[0].scrollHeight}, 0)

            let chat_id = $(this).data('id');
            $.ajax({
                url: "{{ url('/chat-box')}}" + '/' + chat_id + '/messages',
                type: "POST",
                data: {
                    _token: "{{csrf_token()}}"
                },
                success: function (response) {
                    details = '<input type="hidden" value="' + chat_id + '" name="chat_id" class="chat_id">';

                    let cwData = JSON.parse(response.data);

                    $.each(cwData, function (i, sms) {

                        if (sms.send_by === 'to') {
                            details += '<div class="chat chat-left">' +
                                '<div class="chat-avatar">' +
                                '<a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="left" title="" data-original-title="">' +
                                '<img src="{{ asset('images/profile/profile.jpg') }}" alt="avatar" height="40" width="40"/>' +
                                '</a>' +
                                '</div>' +
                                '<div class="chat-body">' +
                                '<div class="chat-content">' +
                                '<p>' + sms.message + '</p>' +
                                '</div>' +
                                '</div>' +
                                '</div>';
                        } else {
                            details += '<div class="chat">' +
                                '<div class="chat-avatar">' +
                                '<a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="right" title="" data-original-title="">' +
                                '<img src="{{ asset('images/profile/profile.jpg') }}" alt="avatar" height="40" width="40"/>' +
                                '</a>' +
                                '</div>' +
                                '<div class="chat-body">' +
                                '<div class="chat-content">' +
                                '<p>' + sms.message + '</p>' +
                                '</div>' +
                                '</div>' +
                                '</div>';
                        }
                    });

                    chatHistory.append(details);
                    chatContainer.animate({scrollTop: chatContainer[0].scrollHeight}, 400)
                }
            });
        });


        // Add message to chat
        function enter_chat(source) {
            let message = $(".message"),
                chatBoxId = $(".chat_id").val(),
                messageValue = message.val();


            $.ajax({
                url: "{{ url('/chat-box')}}" + '/' + chatBoxId + '/reply',
                type: "POST",
                data: {
                    message: messageValue,
                    _token: "{{csrf_token()}}"
                },
                success: function (response) {

                    if (response.status === 'success') {
                        toastr.success(response.message, 'Success!!', {
                            positionClass: 'toast-top-right',
                            containerId: 'toast-top-right',
                            progressBar: true,
                            closeButton: true,
                            newestOnTop: true
                        });

                        let html = '<div class="chat">' +
                            '<div class="chat-avatar">' +
                            '<a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="right" title="" data-original-title="">' +
                            '<img src="{{ asset('images/profile/profile.jpg') }}" alt="avatar" height="40" width="40"/>' +
                            '</a>' +
                            '</div>' +
                            '<div class="chat-body">' +
                            '<div class="chat-content">' +
                            '<p>' + messageValue + '</p>' +
                            '</div>' +
                            '</div>' +
                            '</div>';
                        chatHistory.append(html);
                        message.val("");
                        $(".user-chats").scrollTop($(".user-chats > .chats").height());
                    } else {
                        toastr.warning(response.message, "{{__('locale.labels.attention')}}", {
                            positionClass: 'toast-top-right',
                            containerId: 'toast-top-right',
                            progressBar: true,
                            closeButton: true,
                            newestOnTop: true
                        });
                    }
                },
                error: function (reject) {
                    if (reject.status === 422) {
                        let errors = reject.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            toastr.warning(value[0], "{{__('locale.labels.attention')}}", {
                                positionClass: 'toast-top-right',
                                containerId: 'toast-top-right',
                                progressBar: true,
                                closeButton: true,
                                newestOnTop: true
                            });
                        });
                    } else {
                        toastr.warning(reject.responseJSON.message, "{{__('locale.labels.attention')}}", {
                            positionClass: 'toast-top-right',
                            containerId: 'toast-top-right',
                            progressBar: true,
                            closeButton: true,
                            newestOnTop: true
                        });
                    }
                }
            });


        }
    </script>
@endsection

