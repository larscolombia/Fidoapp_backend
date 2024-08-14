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

    {{-- Advance Filter --}}
    {{--<x-backend.advance-filter>
        <x-slot name="title">
            <h4>{{ __('service.lbl_advanced_filter') }}</h4>
        </x-slot>
        <button type="reset" class="btn btn-danger" id="reset-filter">{{__('product.reset')}}</button>
    </x-backend.advance-filter>--}}

       <!-- Modales -->
    <!-- Modal para editar o crear pasos diarios y meta -->
    <div class="modal fade" id="editOrCreateStepsModal" tabindex="-1" aria-labelledby="editOrCreateStepsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="stepsForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editOrCreateStepsModalLabel">Editar o Crear Pasos Diarios y Meta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="daily_steps" class="form-label">Pasos Diarios</label>
                            <input type="number" class="form-control" id="daily_steps" name="daily_steps">
                        </div>
                        <div class="mb-3">
                            <label for="goal_steps" class="form-label">Meta de Pasos Diarios</label>
                            <input type="number" class="form-control" id="goal_steps" name="goal_steps">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Repite lo mismo para los demás modales -->

    <!-- Modal para editar o crear distancia recorrida y meta -->
    <div class="modal fade" id="editOrCreateDistanceModal" tabindex="-1" aria-labelledby="editOrCreateDistanceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="distanceForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editOrCreateDistanceModalLabel">Editar o Crear Distancia Recorrida y Meta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="distance_covered" class="form-label">Distancia Recorrida (km)</label>
                            <input type="number" step="0.01" class="form-control" id="distance_covered" name="distance_covered">
                        </div>
                        <div class="mb-3">
                            <label for="goal_distance" class="form-label">Meta de Distancia Recorrida (km)</label>
                            <input type="number" step="0.01" class="form-control" id="goal_distance" name="goal_distance">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para editar o crear calorías quemadas y meta -->
    <div class="modal fade" id="editOrCreateCaloriesModal" tabindex="-1" aria-labelledby="editOrCreateCaloriesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="caloriesForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editOrCreateCaloriesModalLabel">Editar o Crear Calorías Quemadas y Meta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="calories_burned" class="form-label">Calorías Quemadas</label>
                            <input type="number" class="form-control" id="calories_burned" name="calories_burned">
                        </div>
                        <div class="mb-3">
                            <label for="goal_calories" class="form-label">Meta de Calorías Quemadas</label>
                            <input type="number" class="form-control" id="goal_calories" name="goal_calories">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para editar o crear minutos activos y meta -->
    <div class="modal fade" id="editOrCreateMinutesModal" tabindex="-1" aria-labelledby="editOrCreateMinutesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="minutesForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editOrCreateMinutesModalLabel">Editar o Crear Minutos Activos y Meta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="active_minutes" class="form-label">Minutos Activos</label>
                            <input type="number" class="form-control" id="active_minutes" name="active_minutes">
                        </div>
                        <div class="mb-3">
                            <label for="goal_active_minutes" class="form-label">Meta de Minutos Activos</label>
                            <input type="number" class="form-control" id="goal_active_minutes" name="goal_active_minutes">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </div>
            </form>
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
            url: '{{ route("backend.activity_levels.mascotas_data") }}',
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Pasos Diarios
        $('#editOrCreateStepsModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var petId = button.data('pet-id');
            var steps = button.data('steps');
            var goalSteps = button.data('goal-steps');

            var modal = $(this);
            modal.find('#daily_steps').val(steps);
            modal.find('#goal_steps').val(goalSteps);
            modal.find('form').attr('action', steps ? '/app/activity-levels/' + petId + '/update-steps' : '/app/activity-levels/' + petId + '/store-steps');
        });

        // Distancia Recorrida
        $('#editOrCreateDistanceModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var petId = button.data('pet-id');
            var distanceCovered = button.data('distance-covered');
            var goalDistance = button.data('goal-distance');

            var modal = $(this);
            modal.find('#distance_covered').val(distanceCovered);
            modal.find('#goal_distance').val(goalDistance);
            modal.find('form').attr('action', distanceCovered ? '/app/activity-levels/' + petId + '/update-distance' : '/app/activity-levels/' + petId + '/store-distance');
        });

        // Calorías Quemadas
        $('#editOrCreateCaloriesModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var petId = button.data('pet-id');
            var caloriesBurned = button.data('calories-burned');
            var goalCalories = button.data('goal-calories');

            var modal = $(this);
            modal.find('#calories_burned').val(caloriesBurned);
            modal.find('#goal_calories').val(goalCalories);
            modal.find('form').attr('action', caloriesBurned ? '/app/activity-levels/' + petId + '/update-calories' : '/app/activity-levels/' + petId + '/store-calories');
        });

        // Minutos Activos
        $('#editOrCreateMinutesModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var petId = button.data('pet-id');
            var activeMinutes = button.data('active-minutes');
            var goalActiveMinutes = button.data('goal-active-minutes');

            var modal = $(this);
            modal.find('#active_minutes').val(activeMinutes);
            modal.find('#goal_active_minutes').val(goalActiveMinutes);
            modal.find('form').attr('action', activeMinutes ? '/app/activity-levels/' + petId + '/update-minutes' : '/app/activity-levels/' + petId + '/store-minutes');
        });
    });
</script>
@endpush
