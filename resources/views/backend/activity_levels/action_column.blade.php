<div class="d-flex gap-2 align-items-center">
    @hasPermission('view_activity_levels')
        @php
            // Verifica si existe un registro de activity_levels para la mascota
            $activityLevel = \App\Models\ActivityLevel::where('pet_id', $pet->id)->first();
        @endphp

        <a href="{{ $activityLevel ? route('backend.activity-levels.edit', ['id' => $activityLevel->id]) : route('backend.activity-levels.create', $pet->id) }}" class="btn btn-sm btn-primary text-white" title="{{ __('activity_levels.View Activity Levels') }}" data-bs-toggle="tooltip">
            Niveles de Actividad
        </a>

        <!-- Botón para editar o crear pasos diarios y meta -->
        <button type="button" class="btn btn-sm btn-primary text-white" data-bs-toggle="modal" data-bs-target="#editOrCreateStepsModal"
            data-pet-id="{{ $pet->id }}"
            data-steps="{{ $activityLevel->daily_steps ?? '' }}"
            data-goal-steps="{{ $activityLevel->goal_steps ?? '' }}"
            title="Editar o Crear Pasos Diarios y Meta">
            <i class="fas fa-walking"></i>
        </button>

        <!-- Botón para editar o crear distancia recorrida y meta -->
        <button type="button" class="btn btn-sm btn-primary text-white" data-bs-toggle="modal" data-bs-target="#editOrCreateDistanceModal"
            data-pet-id="{{ $pet->id }}"
            data-distance-covered="{{ $activityLevel->distance_covered ?? '' }}"
            data-goal-distance="{{ $activityLevel->goal_distance ?? '' }}"
            title="Editar o Crear Distancia Recorrida y Meta">
            <i class="fas fa-ruler-horizontal"></i>
        </button>

        <!-- Botón para editar o crear calorías quemadas y meta -->
        <button type="button" class="btn btn-sm btn-primary text-white" data-bs-toggle="modal" data-bs-target="#editOrCreateCaloriesModal"
            data-pet-id="{{ $pet->id }}"
            data-calories-burned="{{ $activityLevel->calories_burned ?? '' }}"
            data-goal-calories="{{ $activityLevel->goal_calories ?? '' }}"
            title="Editar o Crear Calorías Quemadas y Meta">
            <i class="fas fa-fire"></i>
        </button>

        <!-- Botón para editar o crear minutos activos y meta -->
        <button type="button" class="btn btn-sm btn-primary text-white" data-bs-toggle="modal" data-bs-target="#editOrCreateMinutesModal"
            data-pet-id="{{ $pet->id }}"
            data-active-minutes="{{ $activityLevel->active_minutes ?? '' }}"
            data-goal-active-minutes="{{ $activityLevel->goal_active_minutes ?? '' }}"
            title="Editar o Crear Minutos Activos y Meta">
            <i class="fas fa-stopwatch"></i>
        </button>
    @endhasPermission
</div>