@extends('backend.layouts.app')

@section('title') {{ __('activity_levels.Crear Nivel de Actividad') }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.activity-levels.store', ['pet_id' => $pet->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="pet_name" class="form-label">{{ __('activity_levels.pet_name') }}</label>
                    <input type="text" class="form-control" id="pet_name" name="pet_name" value="{{ $pet->name }}" readonly>
                    <input type="hidden" id="pet_id" name="pet_id" value="{{ $pet->id }}">
                </div>

                <div class="mb-3">
                    <label for="daily_steps" class="form-label">{{ __('activity_levels.daily_steps') }}</label>
                    <input type="number" class="form-control" id="daily_steps" name="daily_steps" placeholder="{{ __('activity_levels.enter_daily_steps') }}">
                </div>

                <div class="mb-3">
                    <label for="distance_covered" class="form-label">{{ __('activity_levels.distance_covered') }}</label>
                    <input type="number" step="0.01" class="form-control" id="distance_covered" name="distance_covered" placeholder="{{ __('activity_levels.enter_distance_covered') }}">
                </div>

                <div class="mb-3">
                    <label for="calories_burned" class="form-label">{{ __('activity_levels.calories_burned') }}</label>
                    <input type="number" class="form-control" id="calories_burned" name="calories_burned" placeholder="{{ __('activity_levels.enter_calories_burned') }}">
                </div>

                <div class="mb-3">
                    <label for="active_minutes" class="form-label">{{ __('activity_levels.active_minutes') }}</label>
                    <input type="number" class="form-control" id="active_minutes" name="active_minutes" placeholder="{{ __('activity_levels.enter_active_minutes') }}">
                </div>

                <div class="mb-3">
                    <label for="goal_steps" class="form-label">{{ __('activity_levels.goal_steps') }}</label>
                    <input type="number" class="form-control" id="goal_steps" name="goal_steps" placeholder="{{ __('activity_levels.enter_goal_steps') }}">
                </div>

                <div class="mb-3">
                    <label for="goal_distance" class="form-label">{{ __('activity_levels.goal_distance') }}</label>
                    <input type="number" step="0.01" class="form-control" id="goal_distance" name="goal_distance" placeholder="{{ __('activity_levels.enter_goal_distance') }}">
                </div>

                <div class="mb-3">
                    <label for="goal_calories" class="form-label">{{ __('activity_levels.goal_calories') }}</label>
                    <input type="number" class="form-control" id="goal_calories" name="goal_calories" placeholder="{{ __('activity_levels.enter_goal_calories') }}">
                </div>

                <div class="mb-3">
                    <label for="goal_active_minutes" class="form-label">{{ __('activity_levels.goal_active_minutes') }}</label>
                    <input type="number" class="form-control" id="goal_active_minutes" name="goal_active_minutes" placeholder="{{ __('activity_levels.enter_goal_active_minutes') }}">
                </div>

                <button type="submit" class="btn btn-primary">{{ __('activity_levels.create_activity_level') }}</button>
            </form>
        </div>
    </div>
@endsection

@push('after-styles')
<link rel="stylesheet" href='{{ mix("modules/product/style.css") }}'>
@endpush

@push('after-scripts')
<script>
    // Aquí podrías agregar cualquier script necesario para manejar el formulario
</script>
@endpush