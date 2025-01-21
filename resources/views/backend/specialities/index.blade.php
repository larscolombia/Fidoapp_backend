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

                <x-backend.quick-action url='{{ route("backend.$module_name.bulk_action") }}'>
                    <div class="">
                        <select name="action_type" class="form-control select2 col-12" id="quick-action-type"
                            style="width:100%">
                            <option value="">{{ __('messages.no_action') }}</option>
                            <option value="delete">{{ __('messages.delete') }}</option>
                        </select>
                    </div>
                </x-backend.quick-action>
                <x-slot name="toolbar">
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text" id="addon-wrapping"><i
                                class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="table_search" class="form-control dt-search"
                            placeholder="{{ __('activity_levels.search_placeholder') }}">
                    </div>
                </x-slot>

                {{-- @hasPermission('add_e-books')
                    <x-buttons.offcanvas :href="route('backend.e-books.create')" title="{{ __('messages.create') }} {{ __('EBooks.title') }}">
                    {{ __('messages.create') }} {{ __('EBooks.title') }}</x-buttons.offcanvas>
                @endhasPermission --}}
            </x-backend.section-header>
            <div class="d-flex justify-content-end">
                <a class="btn btn-primary mt-3 text-end" href="{{ route('backend.specialities.create') }}">{{ __('EBooks.create') }}</a>
            </div>
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>
    </div>

    {{-- <x-backend.advance-filter>
        <x-slot name="title">
            <h4>{{ __('service.lbl_advanced_filter') }}</h4>
        </x-slot>
        <button type="reset" class="btn btn-danger" id="reset-filter">{{__('product.reset')}}</button>
    </x-backend.advance-filter> --}}
@endsection

@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <!-- DataTables Core and Extensions -->
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript" defer>
        const columns = [
            {
                name: 'check',
                data: 'check',
                title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                width: '5%',
                exportable: false,
                orderable: false,
                searchable: false,
            },
            {
                data: 'description',
                name: 'description',
                title: "{{ __('specialities.description') }}",
                orderable: false,
                searchable: true,

            },
            {
                data: 'created_at',
                name: 'created_at',
                title: "{{ __('specialities.created_at') }}",
                orderable: true,

            },
            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('specialities.updated_at') }}",
                orderable: true,

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
                url: '{{ route('backend.specialities.index_datatable') }}',
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
