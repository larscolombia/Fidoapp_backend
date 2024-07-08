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
                    <a class="btn btn-primary" href="{{ route('backend.events.index') }}">{{ __('Eventos.Atrás') }}</a>
                </x-slot>
                {{ __('Eventos.Crear') }}
            </x-backend.section-header>

            <form action="{{ route('backend.events.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="tipo" class="form-label">{{ __('Eventos.Tipo') }}</label>
                    <select class="form-control @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                        <option value="salud">{{ __('Salud') }}</option>
                        <option value="entrenamiento">{{ __('Entrenamiento') }}</option>
                    </select>
                    @error('tipo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="titulo" class="form-label">{{ __('Eventos.Título') }}</label>
                    <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo') }}" required>
                    @error('titulo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="fecha" class="form-label">{{ __('Eventos.Fecha') }}</label>
                    <input type="date" class="form-control @error('fecha') is-invalid @enderror" id="fecha" name="fecha" value="{{ old('fecha') }}" required>
                    @error('fecha')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="hora" class="form-label">{{ __('Eventos.Hora') }}</label>
                    <input type="time" class="form-control @error('hora') is-invalid @enderror" id="hora" name="hora" value="{{ old('hora') }}" required>
                    @error('hora')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="nombre_del_organizador" class="form-label">{{ __('Eventos.Nombre del Organizador') }}</label>
                    <input type="text" class="form-control @error('nombre_del_organizador') is-invalid @enderror" id="nombre_del_organizador" name="nombre_del_organizador" value="{{ old('nombre_del_organizador') }}" required>
                    @error('nombre_del_organizador')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">{{ __('Eventos.Descripción') }} ({{ __('Opcional') }})</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-success">{{ __('Eventos.Crear') }}</button>
                <a href="{{ route('backend.events.index') }}" class="btn btn-secondary">{{ __('Eventos.Cancelar') }}</a>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <iframe src="https://calendar.google.com/calendar/embed?src=your_calendar_id&ctz=America%2FNew_York" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>
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