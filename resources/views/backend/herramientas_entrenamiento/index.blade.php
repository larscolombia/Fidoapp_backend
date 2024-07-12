@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 1050;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <x-slot name="toolbar">
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="table_search" class="form-control dt-search" placeholder="Search...">
                    </div>
                </x-slot>
                @hasPermission('add_herramientas_entrenamiento')
                    <a class="btn btn-primary" href="{{ route('backend.herramientas_entrenamiento.create') }}">{{ __('Crear Herramienta') }}</a>
                @endhasPermission
            </x-backend.section-header>
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>
    </div>
@endsection

@push ('after-styles')
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push ('after-scripts')
<script src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
<script type="text/javascript">
    const columns = [
        {
            data: 'name',
            name: 'name',
            title: "{{ __('Nombre') }}",
            orderable: true,
            searchable: true,
        },
        {
            data: 'type',
            name: 'type',
            title: "{{ __('Tipo') }}",
            orderable: true,
            searchable: true,
        },
        {
            data: 'description',
            name: 'description',
            title: "{{ __('Descripción') }}",
            orderable: false,
            searchable: true,
        },
        {
            data: 'status',
            name: 'status',
            title: "{{ __('Estado') }}",
            orderable: false,
            searchable: true,
        },
        {
            data: 'updated_at',
            name: 'updated_at',
            title: "{{ __('Actualizado') }}",
            orderable: true,
            visible: false,
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
        initDatatable({
            url: '{{ route("backend.herramientas_entrenamiento.index_data") }}',
            finalColumns,
            orderColumn: [[ 1, "asc" ]],
            advanceFilter: () => {
                return {
                    search: $('[name="table_search"]').val(),
                };
            }
        });
    });
</script>
@endpush