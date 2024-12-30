@extends('backend.layouts.app', ['isNoUISlider' => true])

@section('title')
    {{ $module_title }}
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/service/style.css') }}">
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <x-slot name="toolbar">
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text" id="addon-wrapping"><i class="icon-Search"></i></span>
                        <input type="text" class="form-control form-control-sm dt-search"
                            placeholder="{{ __('activity_levels.search_placeholder') }}" aria-label="Search"
                            aria-describedby="addon-wrapping">

                    </div>
                </x-slot>
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
    <!-- DataTables Core and Extensions -->
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript" defer>
        const columns = [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                title: "{{ __('report.lbl_no') }}",
                orderable: false,
                searchable: false
            },
            {
                data: 'user_id',
                name: 'user_id',
                title: "{{ __('report.client') }}",
                searchable: true // Es buscable
            },
            {
                data: 'description',
                name: 'description',
                title: "{{ __('report.description') }}",
                searchable: true // Es buscable
            },

            {
                data: 'amount',
                name: 'amount',
                title: "{{ __('report.amount') }}",
                searchable: false // No es buscable
            },
            {
                data: 'created_at',
                name: 'created_at',
                title: "{{ __('report.date') }}",
                searchable: true // Es buscable
            },
        ]

        let finalColumns = [
            ...columns
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route('backend.reports.fidocoin-transactions-data') }}',
                finalColumns,
            })
        })

        //    $('#booking_date').on('change', function() {
        //       window.renderedDataTable.ajax.reload(null, false)
        //     })
        $('.dt-search').on('keyup change', function() {
            table.columns([1, 2, 4]).search(this.value).draw();
        });
    </script>

    <style>
        .select2-container {
            width: 180px !important;
        }

        .select2-container--default .select2-selection__arrow {
            height: 0 !important;
        }

        .select2-container--default .select2-selection__arrow b {
            margin-top: 15px !important;
        }
    </style>
@endpush
