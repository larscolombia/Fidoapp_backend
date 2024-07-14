@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <x-slot name="toolbar">
                    <a class="btn btn-primary" href="{{ route('backend.mascotas.diarios.index', ['pet' => $pet]) }}">{{ __('Diarios.Back') }}</a>
                </x-slot>
                {{ __('Diarios.create') }}
            </x-backend.section-header>

            <form action="{{ route('backend.mascotas.diarios.store', ['pet' => $pet]) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="date" class="form-label">{{ __('Diarios.date') }}</label>
                    <input type="datetime-local" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date') }}" required>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="actividad" class="form-label">{{ __('Diarios.activity') }}</label>
                    <input type="text" class="form-control @error('actividad') is-invalid @enderror" id="actividad" name="actividad" value="{{ old('actividad') }}" required>
                    @error('actividad')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="notas" class="form-label">{{ __('Diarios.notes') }}</label>
                    <textarea class="form-control @error('notas') is-invalid @enderror" id="notas" name="notas" rows="3">{{ old('notas') }}</textarea>
                    @error('notas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">{{ __('Diarios.create') }}</button>
                <a href="{{ route('backend.mascotas.diarios.index', ['pet' => $pet]) }}" class="btn btn-secondary">{{ __('Diarios.Cancel') }}</a>
            </form>
        </div>
    </div>
@endsection

@push ('after-styles')
<link rel="stylesheet" href='{{ mix("modules/product/style.css") }}'>
@endpush