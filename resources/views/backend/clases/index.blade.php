@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert"
            style="z-index: 1050;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <x-slot name="toolbar">
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text" id="addon-wrapping"><i
                                class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="table_search" class="form-control dt-search"
                            placeholder="{{ __('activity_levels.search_placeholder') }}">
                    </div>
                </x-slot>
                <a class="btn btn-primary"
                    href="{{ route('backend.course_platform.clases.create', ['course' => $course->id]) }}">{{ __('clases.create') }}</a>
            </x-backend.section-header>
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>
    </div>
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script src='{{ mix('modules/product/script.js') }}'></script>
    <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript" defer>
        const columns = [{
                data: 'name',
                name: 'name',
                title: "{{ __('clases.name') }}",
                orderable: true,
                searchable: true,
            },
            {
                data: 'description',
                name: 'description',
                title: "{{ __('clases.description') }}",
                orderable: false,
                searchable: true,
                render: function(data, type, row) {
                    // Limit the description to 50 characters
                    const truncated = data && data.length > 50 ? data.substr(0, 50) + '...' : data;
                    return `<span title="${data}">${truncated}</span>`;
                }
            },
            // {
            //     data: 'url',
            //     name: 'url',
            //     title: "{{ __('clases.url') }}",
            //     orderable: false,
            //     searchable: true,
            //     render: function(data, type, row) {
            //         console.log(data);
            //         // Limit the description to 50 characters
            //         const truncated = data && data.length > 50 ? data.substr(0, 50) + '...' : data;
            //         return `<span title="${data}">${truncated}</span>`;
            //     }
            // },
            {
                data: 'price',
                name: 'price',
                title: "{{ __('clases.price') }}",
                orderable: true,
                searchable: true,
            }
        ];

        const actionColumn = [{
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('service.lbl_action') }}",
            width: '5%'
        }]

        let finalColumns = [
            ...columns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route('backend.course_platform.clases.index_data', ['course' => $course->id]) }}',
                finalColumns,
                orderColumn: [
                    [1, "asc"]
                ],
                advanceFilter: () => {
                    return {
                        search: $('[name="table_search"]').val(),
                    }
                }
            });
        })

        function resetQuickAction() {
            const actionValue = $('#quick-action-type').val();
            if (actionValue != '') {
                $('#quick-action-apply').removeAttr('disabled');

                if (actionValue == 'change-status') {
                    $('.quick-action-field').addClass('d-none');
                    $('#change-status-action').removeClass('d-none');
                } else {
                    $('.quick-action-field').addClass('d-none');
                }

            } else {
                $('#quick-action-apply').attr('disabled', true);
                $('.quick-action-field').addClass('d-none');
            }
        }

        $('#quick-action-type').change(function() {
            resetQuickAction()
        });
    </script>
@endpush
