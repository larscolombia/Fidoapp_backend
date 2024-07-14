@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <label for="mascota" class="form-label">{{ __('Diarios.name_mascota') }}</label>
                <input type="text" class="form-control" id="mascota" name="mascota" value="{{ $diario->pet->name }}" readonly>
            </div>

            <div class="mb-3">
                <label for="date" class="form-label">{{ __('Diarios.date') }}</label>
                <input type="text" class="form-control" id="date" name="date" value="{{ $diario->date }}" readonly>
            </div>

            <div class="mb-3">
                <label for="actividad" class="form-label">{{ __('Diarios.activity') }}</label>
                <input type="text" class="form-control" id="actividad" name="actividad" value="{{ $diario->actividad }}" readonly>
            </div>

            <div class="mb-3">
                <label for="notas" class="form-label">{{ __('Diarios.notes') }}</label>
                <textarea class="form-control" id="notas" name="notas" rows="3" readonly>{{ $diario->notas }}</textarea>
            </div>

            <div class="mt-4">
                <a href="{{ route('backend.mascotas.diarios.edit', ['pet' => $diario->pet_id, 'diario' => $diario->id]) }}" class="btn btn-primary">{{ __('Diarios.Edit') }}</a>
                <a href="{{ route('backend.mascotas.diarios.index', ['pet' => $diario->pet_id]) }}" class="btn btn-secondary">{{ __('Diarios.Back') }}</a>
            </div>
        </div>
    </div>
@endsection

@push ('after-styles')
<link rel="stylesheet" href='{{ mix("modules/product/style.css") }}'>
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush