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
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 w-100">
                <div class="d-flex flex-wrap align-items-center">
                    <div class="input-group flex-nowrap me-3 mb-2">
                        <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="table_search" class="form-control dt-search" placeholder="Search...">
                    </div>
                    <div class="me-3 mb-2" style="min-width: 150px;">
                        <label for="filter_is_favorite" class="form-label">{{ __('comando_entrenamiento.is_favorite') }}</label>
                        <select id="filter_is_favorite" class="form-control">
                            <option value="">{{ __('Todos') }}</option>
                            <option value="1">{{ __('Sí') }}</option>
                            <option value="0">{{ __('No') }}</option>
                        </select>
                    </div>
                    <div class="mb-2" style="min-width: 150px;">
                        <label for="filter_category" class="form-label">{{ __('comando_entrenamiento.category') }}</label>
                        <select id="filter_category" class="form-control">
                            <option value="">{{ __('Todas') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-2">
                    @hasPermission('add_comandos')
                        <a class="btn btn-primary" href="{{ route('backend.comandos.create') }}">{{ __('comando_entrenamiento.create') }}</a>
                    @endhasPermission
                </div>
            </div>
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>
    </div>

@endsection

@push ('after-styles')
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 34px;
        height: 20px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 20px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:checked + .slider:before {
        transform: translateX(14px);
    }
</style>
@endpush

@push ('after-scripts')
<script src='{{ mix("modules/product/script.js") }}'></script>
<script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

<script type="text/javascript" defer>
    const columns = [
        {
            data: 'name',
            name: 'name',
            title: "{{ __('comando_entrenamiento.name') }}",
            orderable: true,
            searchable: true,
        },
        {
            data: 'type',
            name: 'type',
            title: "{{ __('comando_entrenamiento.type') }}",
            orderable: false,
            searchable: true,
        },
        {
            data: 'description',
            name: 'description',
            title: "{{ __('comando_entrenamiento.description') }}",
            orderable: false,
            searchable: true,
        },
        {
            data: 'is_favorite',
            name: 'is_favorite',
            title: "{{ __('comando_entrenamiento.is_favorite') }}",
            orderable: false,
            searchable: true,
            render: function(data, type, row) {
                console.log(data)
                const checked = data == 'Sí' ? 'checked' : '';
                return `<label class="switch">
                            <input type="checkbox" class="toggle-favorite" data-id="${row.id}" ${checked}>
                            <span class="slider round"></span>
                        </label>`;
            }
        },
        {
            data: 'category',
            name: 'category',
            title: "{{ __('comando_entrenamiento.category') }}",
            orderable: false,
            searchable: true,
        },
        {
            data: 'updated_at',
            name: 'updated_at',
            title: "{{ __('product.lbl_update_at') }}",
            orderable: true,
            visible: false,
        },
    ];

    const actionColumn = [{
        data: 'action',
        name: 'action',
        orderable: false,
        searchable: false,
        title: "{{ __('service.lbl_action') }}",
        width: '5%'
    }];

    let finalColumns = [
        ...columns,
        ...actionColumn
    ];

    document.addEventListener('DOMContentLoaded', (event) => {
        const table = initDatatable({
            url: '{{ route("backend.comandos.index_data") }}',
            finalColumns,
            orderColumn: [[ 1, "asc" ]],
            advanceFilter: () => {
                return {
                    search: $('[name="table_search"]').val(),
                    is_favorite: $('#filter_is_favorite').val(),
                    category_id: $('#filter_category').val(),
                };
            }
        });

        $('#filter_is_favorite, #filter_category').on('change', function() {
            table.ajax.reload();
        });

        $('#datatable').on('change', '.toggle-favorite', function() {
            const comandoId = $(this).data('id');
            const isFavorite = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '{{ route("backend.comandos.toggle_favorite") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: comandoId,
                    is_favorite: isFavorite,
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Favorite status updated');
                    } else {
                        console.log('Failed to update favorite status');
                    }
                }
            });
        });
    });
</script>
@endpush