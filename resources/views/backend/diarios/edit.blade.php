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
                    <a class="btn btn-primary" href="{{ route('backend.mascotas.diarios.index', ['pet' => $pet]) }}">{{ __('Diarios.Back') }}</a>
                </x-slot>
                {{ __('Diarios.Edit') }}
            </x-backend.section-header>

            <form action="{{ route('backend.mascotas.diarios.update', ['pet' => $pet, 'diario' => $diario->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="date" class="form-label">{{ __('Diarios.Date') }}</label>
                    <input type="datetime-local" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $diario->date) }}" required>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="actividad" class="form-label">{{ __('Diarios.Activity') }}</label>
                    <input type="text" class="form-control @error('actividad') is-invalid @enderror" id="actividad" name="actividad" value="{{ old('actividad', $diario->actividad) }}" required>
                    @error('actividad')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="notas" class="form-label">{{ __('Diarios.Notes') }}</label>
                    <textarea class="form-control @error('notas') is-invalid @enderror" id="notas" name="notas" rows="3">{{ old('notas', $diario->notas) }}</textarea>
                    @error('notas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">{{ __('Diarios.Update') }}</button>
                <a href="{{ route('backend.mascotas.diarios.index', ['pet' => $pet]) }}" class="btn btn-secondary">{{ __('Diarios.Cancel') }}</a>
            </form>
        </div>
    </div>
@endsection

@push ('after-styles')
<link rel="stylesheet" href='{{ mix("modules/product/style.css") }}'>
@endpush