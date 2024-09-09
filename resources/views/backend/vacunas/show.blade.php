@extends('backend.layouts.app')

@section('title') {{ __('Detalles de la Vacuna') }} @endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>{{ __('Vacuna de ') }} {{ $pet->name }}</h4>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">{{ __('Nombre de la Vacuna') }}</label>
                <p class="form-control-plaintext">{{ $vacuna->vacuna_name }}</p>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('Fecha de Aplicación') }}</label>
                <p class="form-control-plaintext">{{ $vacuna->fecha_aplicacion }}</p>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('Fecha de Refuerzo') }}</label>
                <p class="form-control-plaintext">{{ $vacuna->fecha_refuerzo_vacuna }}</p>
            </div>

            <a href="{{ route('backend.mascotas.vacunas.index', ['pet' => $pet->id]) }}" class="btn btn-secondary">{{ __('Volver') }}</a>
            <a href="{{ route('backend.mascotas.vacunas.edit', ['pet' => $pet->id, 'vacuna' => $vacuna->id]) }}" class="btn btn-primary">{{ __('Editar') }}</a>
        </div>
    </div>
@endsection
