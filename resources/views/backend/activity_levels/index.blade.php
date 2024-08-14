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
<script src='{{ mix("modules/product/script.js") }}'></script>
<script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

<script type="text/javascript" defer>
    const columns = [
        {
            data: 'daily_steps',
            name: 'daily_steps',
            title: "{{ __('activity_levels.daily_steps') }}",
            orderable: true,
            searchable: true,
        },
        {
            data: 'distance_covered',
            name: 'distance_covered',
            title: "{{ __('activity_levels.distance_covered') }}",
            orderable: true,
            searchable: true,
        },
        {
            data: 'calories_burned',
            name: 'calories_burned',
            title: "{{ __('activity_levels.calories_burned') }}",
            orderable: true,
            searchable: true,
        },
        {
            data: 'active_minutes',
            name: 'active_minutes',
            title: "{{ __('activity_levels.active_minutes') }}",
            orderable: true,
            searchable: true,
        },
    ];

    const actionColumn = [{
        data: 'action',
        name: 'action',
        orderable: false,
        searchable: false,
        title: "{{ __('activity_levels.actions') }}",
        width: '5%'
    }];

    let finalColumns = [
        ...columns,
        ...actionColumn
    ];

    document.addEventListener('DOMContentLoaded', (event) => {
        const table = initDatatable({
            url: '{{ route("backend.activity_levels.index_data", ["pet_id" => $pet_id]) }}',
            finalColumns,
            orderColumn: [[ 1, "asc" ]],
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
