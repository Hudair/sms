@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Chat Box'))

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-chat.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-chat-list.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">

@endsection

@section('content-sidebar')
    @include('customer.ChatBox._sidebar')
@endsection


@section('content')
    <div class="body-content-overlay"></div>
    <!-- Main chat area -->
    <section class="chat-app-window">
        <!-- To load Conversation -->
        <div class="start-chat-area">
            <div class="mb-1 start-chat-icon">
                <i data-feather="message-square"></i>
            </div>
            <h4 class="sidebar-toggle start-chat-text">
                <a href="{{ route('customer.chatbox.new') }}" class="text-dark">{{ __('locale.labels.new_conversion') }}</a>
            </h4>
        </div>
        <!--/ To load Conversation -->

        <!-- Active Chat -->
        <div class="active-chat d-none">
            <!-- Chat Header -->
            <div class="chat-navbar">
                <header class="chat-header">
                    <div class="d-flex align-items-center">
                        <div class="sidebar-toggle d-block d-lg-none me-1">
                            <i data-feather="menu" class="font-medium-5"></i>
                        </div>
                        <div class="avatar avatar-border user-profile-toggle m-0 me-1"></div>
                    </div>
                    <div class="d-flex align-items-center">

                        <span class="add-to-blacklist" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('locale.labels.block') }}"> <i data-feather="shield" class="cursor-pointer d-sm-block d-none font-medium-2 mx-1 text-primary"></i> </span>

                        <span class="remove-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('locale.buttons.delete') }}"><i data-feather="trash" class="cursor-pointer d-sm-block d-none font-medium-2 text-danger"></i></span>

                    </div>
                </header>
            </div>
            <!--/ Chat Header -->

            <!-- User Chat messages -->
            <div class="user-chats">
                <div class="chats">
                    <div class="chat">
                        <div class="chat_history"></div>
                    </div>
                </div>
            </div>
            <!-- User Chat messages -->

            <!-- Submit Chat form -->
            <form class="chat-app-form" action="javascript:void(0);" onsubmit="enter_chat();">
                <div class="input-group input-group-merge me-1 form-send-message">
                    <input type="text" class="form-control message" placeholder="{{ __('locale.campaigns.type_your_message') }}"/>
                </div>
                <button type="button" class="btn btn-primary send" onclick="enter_chat();">
                    <i data-feather="send" class="d-lg-none"></i>
                    <span class="d-none d-lg-block">{{ __('locale.buttons.send') }}</span>
                </button>
            </form>
            <!--/ Submit Chat form -->
        </div>
        <!--/ Active Chat -->
    </section>
    <!--/ Main chat area -->
@endsection

@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/scripts/pages/chat.js')) }}"></script>


    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>

    <script>
        // autoscroll to bottom of Chat area
        let chatContainer = $(".user-chats"),
            details,
            chatHistory = $(".chat_history");

        $(".chat-users-list li").on("click", function () {

            chatHistory.empty();
            chatContainer.animate({scrollTop: chatContainer[0].scrollHeight}, 0)

            let chat_id = $(this).data('id');

            console.log(chat_id);

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
                        let media_url = '';
                        if (sms.media_url !== null) {
                            media_url = '<p><img src="' + sms.media_url + '"/></p>';
                        }

                        let message = '';
                        if (sms.message !== null) {
                            message = '<p>' + sms.message + '</p>';
                        }

                        if (sms.send_by === 'to') {
                            details += '<div class="chat chat-left">' +
                                '<div class="chat-avatar">' +
                                '<span class="avatar box-shadow-1 cursor-pointer">' +
                                '<img src="{{asset('images/profile/profile.jpg')}}" alt="avatar" height="36" width="36"/>' +
                                '</span>' +
                                '</div>' +
                                '<div class="chat-body">' +
                                '<div class="chat-content">' +
                                media_url +
                                message +
                                '</div>' +
                                '</div>' +
                                '</div>';
                        } else {
                            details += '<div class="chat">' +
                                '<div class="chat-avatar">' +
                                '<span class="avatar box-shadow-1 cursor-pointer">' +
                                '<img src="{{  route('user.avatar', Auth::user()->uid) }}" alt="avatar" height="36" width="36"/>' +
                                '</span>' +
                                '</div>' +
                                '<div class="chat-body">' +
                                '<div class="chat-content">' +
                                message +
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
                        toastr['success'](response.message, 'Success!!', {
                            closeButton: true,
                            positionClass: 'toast-top-right',
                            progressBar: true,
                            newestOnTop: true,
                            rtl: isRtl
                        });

                        let html = '<div class="chat">' +
                            '<div class="chat-avatar">' +
                            '<span class="avatar box-shadow-1 cursor-pointer">' +
                            '<img src="{{ asset('images/profile/profile.jpg') }}" alt="avatar" height="36" width="36"/>' +
                            '</span>' +
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
                        toastr['warning'](response.message, "{{ __('locale.labels.attention') }}", {
                            closeButton: true,
                            positionClass: 'toast-top-right',
                            progressBar: true,
                            newestOnTop: true,
                            rtl: isRtl
                        });
                    }
                },
                error: function (reject) {
                    if (reject.status === 422) {
                        let errors = reject.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            toastr['warning'](value[0], "{{__('locale.labels.attention')}}", {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                                rtl: isRtl
                            });
                        });
                    } else {
                        toastr['warning'](reject.responseJSON.message, "{{__('locale.labels.attention')}}", {
                            closeButton: true,
                            positionClass: 'toast-top-right',
                            progressBar: true,
                            newestOnTop: true,
                            rtl: isRtl
                        });
                    }
                }
            });


        }

        // Pusher.logToConsole = true;
        Echo.private('chat').listen('MessageReceived', (e) => {

            chatHistory.empty();
            chatContainer.animate({scrollTop: chatContainer[0].scrollHeight}, 0)

            let chat_id = e.data.uid;
            $.ajax({
                url: "{{ url('/chat-box')}}" + '/' + chat_id + '/notification',
                type: "POST",
                data: {
                    _token: "{{csrf_token()}}"
                },
                success: function (response) {
                    details = '<input type="hidden" value="' + chat_id + '" name="chat_id" class="chat_id">';

                    $(".media-list li").each(function () {

                        let uid = $(this).data('id');

                        if (chat_id === uid) {
                            let value = "li[data-id=" + uid + "]";
                            let notification = $(this).find('.counter');
                            notification.removeAttr('hidden');
                            $(value + " .notification_count").html(e.data.notification);
                        }
                    });

                    let cwData = JSON.parse(response.data);

                    $.each(cwData, function (i, sms) {
                        let media_url = '';
                        if (sms.media_url !== null) {
                            media_url = '<p><img src="' + sms.media_url + '"/></p>';
                        }

                        let message = '';
                        if (sms.message !== null) {
                            message = '<p>' + sms.message + '</p>';
                        }

                        if (sms.send_by === 'to') {
                            details += '<div class="chat chat-left">' +
                                '<div class="chat-avatar">' +
                                '<span class="avatar box-shadow-1 cursor-pointer">' +
                                '<img src="{{asset('images/profile/profile.jpg')}}" alt="avatar" height="36" width="36"/>' +
                                '</span>' +
                                '</div>' +
                                '<div class="chat-body">' +
                                '<div class="chat-content">' +
                                media_url +
                                message +
                                '</div>' +
                                '</div>' +
                                '</div>';
                        } else {
                            details += '<div class="chat">' +
                                '<div class="chat-avatar">' +
                                '<a class="avatar box-shadow-1 cursor-pointer">' +
                                '<img src="{{  route('user.avatar', Auth::user()->uid) }}" alt="avatar" height="36" width="36"/>' +
                                '</a>' +
                                '</div>' +
                                '<div class="chat-body">' +
                                '<div class="chat-content">' +
                                message +
                                '</div>' +
                                '</div>' +
                                '</div>';
                        }
                    });


                    chatHistory.append(details);
                    chatContainer.animate({scrollTop: chatContainer[0].scrollHeight}, 400)
                }
            });
        })


        $(".remove-btn").on('click', function (event) {
            event.preventDefault();
            let sms_id = $(".chat_id").val();

            Swal.fire({
                title: "{{ __('locale.labels.are_you_sure') }}",
                text: "{{ __('locale.labels.able_to_revert') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "{{ __('locale.labels.delete_it') }}",
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: "{{ url('/chat-box')}}" + '/' + sms_id + '/delete',
                        type: "POST",
                        data: {
                            _token: "{{csrf_token()}}"
                        },
                        success: function (response) {

                            if (response.status === 'success') {
                                toastr['success'](response.message, '{{__('locale.labels.success')}}!!', {
                                    closeButton: true,
                                    positionClass: 'toast-top-right',
                                    progressBar: true,
                                    newestOnTop: true,
                                    rtl: isRtl
                                });

                                setTimeout(function () {
                                    window.location.reload(); // then reload the page.(3)
                                }, 3000);

                            } else {
                                toastr['warning'](response.message, '{{ __('locale.labels.warning') }}!', {
                                    closeButton: true,
                                    positionClass: 'toast-top-right',
                                    progressBar: true,
                                    newestOnTop: true,
                                    rtl: isRtl
                                });
                            }
                        },
                        error: function (reject) {
                            if (reject.status === 422) {
                                let errors = reject.responseJSON.errors;
                                $.each(errors, function (key, value) {
                                    toastr['warning'](value[0], "{{__('locale.labels.attention')}}", {
                                        closeButton: true,
                                        positionClass: 'toast-top-right',
                                        progressBar: true,
                                        newestOnTop: true,
                                        rtl: isRtl
                                    });
                                });
                            } else {
                                toastr['warning'](reject.responseJSON.message, "{{__('locale.labels.attention')}}", {
                                    closeButton: true,
                                    positionClass: 'toast-top-right',
                                    progressBar: true,
                                    newestOnTop: true,
                                    rtl: isRtl
                                });
                            }
                        }
                    });
                }
            })

        })

        $(".add-to-blacklist").on('click', function (event) {
            event.preventDefault();
            let sms_id = $(".chat_id").val();

            Swal.fire({
                title: "{{ __('locale.labels.are_you_sure') }}",
                text: "{{ __('locale.labels.remove_blacklist') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "{{ __('locale.labels.block') }}",
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: "{{ url('/chat-box')}}" + '/' + sms_id + '/block',
                        type: "POST",
                        data: {
                            _token: "{{csrf_token()}}"
                        },
                        success: function (response) {

                            if (response.status === 'success') {
                                toastr['success'](response.message, '{{__('locale.labels.success')}}!!', {
                                    closeButton: true,
                                    positionClass: 'toast-top-right',
                                    progressBar: true,
                                    newestOnTop: true,
                                    rtl: isRtl
                                });

                                setTimeout(function () {
                                    window.location.reload(); // then reload the page.(3)
                                }, 3000);

                            } else {
                                toastr['warning'](response.message, '{{ __('locale.labels.warning') }}!', {
                                    closeButton: true,
                                    positionClass: 'toast-top-right',
                                    progressBar: true,
                                    newestOnTop: true,
                                    rtl: isRtl
                                });
                            }
                        },
                        error: function (reject) {
                            if (reject.status === 422) {
                                let errors = reject.responseJSON.errors;
                                $.each(errors, function (key, value) {
                                    toastr['warning'](value[0], "{{__('locale.labels.attention')}}", {
                                        closeButton: true,
                                        positionClass: 'toast-top-right',
                                        progressBar: true,
                                        newestOnTop: true,
                                        rtl: isRtl
                                    });
                                });
                            } else {
                                toastr['warning'](reject.responseJSON.message, "{{__('locale.labels.attention')}}", {
                                    closeButton: true,
                                    positionClass: 'toast-top-right',
                                    progressBar: true,
                                    newestOnTop: true,
                                    rtl: isRtl
                                });
                            }
                        }
                    });
                }
            })

        })

    </script>
@endsection

