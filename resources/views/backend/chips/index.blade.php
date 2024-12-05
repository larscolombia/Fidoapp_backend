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
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 w-100">
                <div class="d-flex flex-wrap align-items-center">
                    <div class="input-group flex-nowrap me-3 mb-2">
                        <span class="input-group-text" id="addon-wrapping"><i
                                class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="table_search" class="form-control dt-search"
                            placeholder="{{ __('activity_levels.search_placeholder') }}">
                    </div>
                    <div class="me-3 mb-2" style="min-width: 150px;">
                        <label for="filter_fecha_implantacion" class="form-label">{{ __('Fecha de Implantación') }}</label>
                        <input type="date" id="filter_fecha_implantacion" class="form-control">
                    </div>
                    <div class="mb-2" style="min-width: 150px;">
                        <label for="filter_nombre_fabricante" class="form-label">{{ __('Fabricante') }}</label>
                        <input type="text" id="filter_nombre_fabricante" class="form-control"
                            placeholder="Nombre fabricante">
                    </div>
                </div>
                <div class="mb-2">
                    @hasPermission('add_chips')
                        <a class="btn btn-primary" href="{{ route('backend.chips.create') }}">{{ __('Crear Chip') }}</a>
                    @endhasPermission
                </div>
            </div>
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
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript" defer>
        const columns = [{
                data: 'num_identificacion',
                name: 'num_identificacion',
                title: "{{ __('Número de Identificación') }}",
                orderable: true,
                searchable: true,
            },
            {
                data: 'fecha_implantacion',
                name: 'fecha_implantacion',
                title: "{{ __('Fecha de Implantación') }}",
                orderable: true,
                searchable: true,
            },
            {
                data: 'nombre_fabricante',
                name: 'nombre_fabricante',
                title: "{{ __('Fabricante') }}",
                orderable: true,
                searchable: true,
            },
            {
                data: 'num_contacto',
                name: 'num_contacto',
                title: "{{ __('Número de Contacto') }}",
                orderable: true,
                searchable: true,
            },
        ];

        const actionColumn = [{
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            title: "{{ __('Acciones') }}",
            width: '5%'
        }];

        let finalColumns = [
            ...columns,
            ...actionColumn
        ];

        document.addEventListener('DOMContentLoaded', (event) => {
            const table = initDatatable({
                url: '{{ route('backend.chips.index_data') }}',
                finalColumns,
                orderColumn: [
                    [1, "asc"]
                ],
                advanceFilter: () => {
                    return {
                        search: $('[name="table_search"]').val(),
                        fecha_implantacion: $('#filter_fecha_implantacion').val(),
                        nombre_fabricante: $('#filter_nombre_fabricante').val(),
                    };
                }
            });

            $('#filter_fecha_implantacion, #filter_nombre_fabricante').on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endpush
