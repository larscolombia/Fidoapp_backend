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
            </x-backend.section-header>
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>
    </div>

    {{--<x-backend.advance-filter>
        <x-slot name="title">
            <h4>{{ __('service.lbl_advanced_filter') }}</h4>
        </x-slot>
        <button type="reset" class="btn btn-danger" id="reset-filter">{{__('product.reset')}}</button>
    </x-backend.advance-filter>--}}
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
            data: 'owner_name',
            name: 'owner_name',
            title: "{{ __('diarios.Owner Name') }}",
            orderable: true,
            searchable: true,
        },
        {
            data: 'pet_name',
            name: 'pet_name',
            title: "{{ __('diarios.Pet Name') }}",
            orderable: true,
            searchable: true,
        },
        {
            data: 'breed',
            name: 'breed',
            title: "{{ __('diarios.Breed') }}",
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
            url: '{{ route("backend.vacunas.mascotas_data") }}',
            finalColumns,
            orderColumn: [[ 1, "asc" ]],
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