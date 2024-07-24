@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <x-slot name="toolbar">
                    <a class="btn btn-primary" href="{{ route('backend.herramientas_entrenamiento.index') }}">{{ __('herramientas_entrenamiento.Back') }}</a>
                </x-slot>
                {{ __('herramientas_entrenamiento.Detalles') }}
            </x-backend.section-header>

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('herramientas_entrenamiento.Nombre') }}</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $herramienta->name }}" readonly>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">{{ __('herramientas_entrenamiento.Descripción') }}</label>
                <textarea class="form-control" id="description" name="description" rows="3" readonly>{{ $herramienta->description }}</textarea>
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">{{ __('herramientas_entrenamiento.Tipo') }}</label>
                <input type="text" class="form-control" id="type" name="type" value="{{ $herramienta->type->type }}" readonly>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">{{ __('herramientas_entrenamiento.status') }}</label>
                @if($herramienta->status == 'active' || $herramienta->status == 'Active')
                    <input type="text" class="form-control" id="status" name="status" value="{{ __('herramientas_entrenamiento.Activo') }}" readonly>
                @else   
                    <input type="text" class="form-control" id="status" name="status" value="{{ __('herramientas_entrenamiento.Inactivo') }}" readonly>
                @endif 
            </div>

            <div class="mb-3">
                <label for="audio" class="form-label">{{ __('herramientas_entrenamiento.Audio') }}</label>
                @if($herramienta->audio)
                    <audio controls>
                        <source src="{{ asset($herramienta->audio) }}" type="audio/mpeg">
                        {{ __('herramientas_entrenamiento.Your browser does not support the audio element.') }}
                    </audio>
                @else
                    <p>{{ __('herramientas_entrenamiento.No audio available') }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@push ('after-styles')
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush