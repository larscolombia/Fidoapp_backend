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
                    <a class="btn btn-primary" href="{{ route('backend.herramientas_entrenamiento.index') }}">{{ __('herramientas_entrenamiento.Back') }}</a>
                </x-slot>
                {{ __('Herramientas_entrenamiento.Editar') }}
            </x-backend.section-header>

            <form action="{{ route('backend.herramientas_entrenamiento.update', $herramienta->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('herramientas_entrenamiento.Nombre') }}</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $herramienta->name) }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('herramientas_entrenamiento.Descripción') }}</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $herramienta->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">{{ __('Tipo') }}</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="clicker" {{ $herramienta->type == 'clicker' ? 'selected' : '' }}>{{ __('herramientas_entrenamiento.Clicker') }}</option>
                        <option value="silbato" {{ $herramienta->type == 'silbato' ? 'selected' : '' }}>{{ __('herramientas_entrenamiento.Silbato') }}</option>
                        <option value="diarios" {{ $herramienta->type == 'diarios' ? 'selected' : '' }}>{{ __('herramientas_entrenamiento.Diarios') }}</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">{{ __('herramientas_entrenamiento.status') }}</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="active" {{ $herramienta->status == 'active' || $herramienta->status == 'Active' ? 'selected' : '' }}>{{ __('herramientas_entrenamiento.Activo') }}</option>
                        <option value="inactive" {{ $herramienta->status == 'inactive' || $herramienta->status == 'Inactive' ? 'selected' : '' }}>{{ __('herramientas_entrenamiento.Inactivo') }}</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">{{ __('herramientas_entrenamiento.Actualizar Herramienta') }}</button>
                <a href="{{ route('backend.herramientas_entrenamiento.index') }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
            </form>
        </div>
    </div>
@endsection

@push ('after-styles')
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush