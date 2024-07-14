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
                @hasPermission('add_diarios')
                    <a class="btn btn-primary" href="{{ route('backend.mascotas.diarios.create', ['pet' => $pet]) }}">{{ __('diarios.create') }}</a>
                @endhasPermission
                {{-- @hasPermission('add_e-books')
                    <x-buttons.offcanvas :href="route('backend.e-books.create')" title="{{ __('messages.create') }} {{ __('EBooks.title') }}">
                    {{ __('messages.create') }} {{ __('EBooks.title') }}</x-buttons.offcanvas>
                @endhasPermission --}}
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
                data: 'mascota',
                name: 'mascota',
                title: "{{ __('diarios.name_mascota') }}",
                orderable: true,
                searchable: true,
            },
            {
                data: 'date',
                name: 'date',
                title: "{{ __('diarios.date') }}",
                orderable: false,
                searchable: true,
            },
            {
                data: 'activity',
                name: 'activity',
                title: "{{ __('diarios.activity') }}",
                orderable: false,
                searchable: true,
            }, 
            {
                data: 'notes',
                name: 'notes',
                title: "{{ __('diarios.notes') }}",
                orderable: false,
                searchable: true,
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
                url: '{{ route("backend.mascotas.diarios.diarios_data", ["pet" => $pet]) }}',
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

