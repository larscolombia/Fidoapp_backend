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
                @hasPermission('add_courses')
                    <a class="btn btn-primary" href="{{ route('backend.courses.create') }}">{{ __('Courses.create') }}</a>
                @endhasPermission
            </x-backend.section-header>
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>
    </div>

@endsection

@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script src='{{ mix('modules/product/script.js') }}'></script>
    <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
    <!-- DataTables Core and Extensions -->
    <script src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
    <script>
        const columns = [{
                data: 'titulo',
                name: 'titulo',
                title: "{{ __('Courses.title_table') }}",
                orderable: true,
                searchable: true,
            },
            {
                data: 'enlace',
                name: 'enlace',
                title: "{{ __('Courses.enlace') }}",
                orderable: true,
                searchable: true,
            },
            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('product.lbl_update_at') }}",
                orderable: true,
                visible: false,
            },
        ]

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
                url: '{{ route('backend.courses.index_data') }}',
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
