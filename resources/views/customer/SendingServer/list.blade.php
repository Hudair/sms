@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Sending Servers'))

@section('vendor-style')
    {{-- vendor files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection
@section('page-style')
    {{-- Page css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">
@endsection

@section('content')
    {{-- Data list view starts --}}
    <section id="data-list-view" class="data-list-view-header">
        {{-- DataTable starts --}}
        <div class="table-responsive">
            <table class="table data-list-view">
                <thead>
                <tr>
                    <th></th>
                    <th>{{__('locale.labels.name')}}</th>
                    <th>{{__('locale.labels.type')}}</th>
                    <th>{{__('locale.labels.actions')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($sending_servers as $server)
                    @if($server["type"] === 'http')
                        @php $color = "success" @endphp
                    @elseif($server["type"] === 'smpp')
                        @php $color = "primary" @endphp
                    @elseif($server["type"] === 'whatsapp')
                        @php $color = "info" @endphp
                    @endif

                    @if($server['custom'] == 1)
                        @php $type = 'custom' @endphp
                    @else
                        @php $type = $server->settings @endphp
                    @endif

                    <tr>
                        <td></td>
                        <td>{{ $server['name'] }}</td>
                        <td>
                            <div class="chip chip-{{$color}}">
                                <div class="chip-body">
                                    <div class="chip-text">{{ strtoupper($server['type'])}}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="{{route('customer.sending-servers.create', ['type' => $type])}}" class="btn btn-primary btn-sm">{{__('locale.labels.choose')}}</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{-- DataTable ends --}}

    </section>
    <br>
    {{-- Data list view end --}}
@endsection
@section('vendor-script')
    {{-- vendor js files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
@endsection
@section('page-script')
    <script>
        $(document).ready(function () {
            "use strict"
            // init list view datatable

            $(".data-list-view").DataTable({

                "processing": true,
                "columns": [
                    {"data": "id", orderable: false, searchable: false},
                    {"data": "name"},
                    {"data": "type"},
                    {"data": "action", orderable: false, searchable: false}
                ],
                responsive: false,
                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                        targets: 0,
                    }
                ],
                dom:
                    '<"top"<"actions action-btns"B><"action-filters"lf>><"clear">rt<"bottom"<"actions">p>',
                oLanguage: {
                    sLengthMenu: "_MENU_",
                    sZeroRecords: "{{ __('locale.datatables.no_results') }}",
                    sSearch: "",
                    sProcessing: "{{ __('locale.datatables.processing') }}",
                    oPaginate: {
                        sFirst: "{{ __('locale.datatables.first') }}",
                        sPrevious: "{{ __('locale.datatables.previous') }}",
                        sNext: "{{ __('locale.datatables.next') }}",
                        sLast: "{{ __('locale.datatables.last') }}"
                    }
                },
                aLengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
                order: [[0, "asc"]],
                bInfo: false,
                pageLength: 10,
                buttons: [],
                initComplete: function () {
                    $(".dt-buttons .btn").removeClass("btn-secondary")
                }
            });

            // To append actions dropdown before add new button
            let actionDropdown = $(".add-new-div")
            actionDropdown.insertBefore($(".top .actions .dt-buttons"))

            // Scrollbar
            if ($(".data-items").length > 0) {
                new PerfectScrollbar(".data-items", {wheelPropagation: false})
            }

        });

    </script>
@endsection
