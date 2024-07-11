@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <x-slot name="toolbar">
                    <a class="btn btn-primary" href="{{ route('backend.comandos.index') }}">{{ __('comando_entrenamiento.back') }}</a>
                </x-slot>
                {{ __('comando_entrenamiento.show') }}
            </x-backend.section-header>

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('comando_entrenamiento.name') }}</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $comando->name }}" disabled>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">{{ __('comando_entrenamiento.description') }}</label>
                <textarea class="form-control" id="description" name="description" rows="3" disabled>{{ $comando->description }}</textarea>
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">{{ __('comando_entrenamiento.type') }}</label>
                <input type="text" class="form-control" id="type" name="type" value="{{ $comando->type }}" disabled>
            </div>

            <div class="mb-3">
                <label for="is_favorite" class="form-label">{{ __('comando_entrenamiento.is_favorite') }}</label>
                <input type="text" class="form-control" id="is_favorite" name="is_favorite" value="{{ $comando->is_favorite ? __('Sí') : __('No') }}" disabled>
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">{{ __('comando_entrenamiento.category') }}</label>
                <input type="text" class="form-control" id="category" name="category" value="{{ $comando->category->name }}" disabled>
            </div>

            <div class="mb-3">
                <label for="instructions" class="form-label">{{ __('comando_entrenamiento.instructions') }}</label>
                <textarea class="form-control" id="instructions" name="instructions" rows="5" disabled>{{ $comando->instructions }}</textarea>
            </div>
        </div>
    </div>
@endsection

@push('after-styles')
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
<script src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Aquí puedes añadir cualquier JavaScript adicional si es necesario
    });
</script>
@endpush