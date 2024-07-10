@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <x-slot name="toolbar">
                    <a class="btn btn-primary" href="{{ route('backend.events.index') }}">{{ __('event.Atras') }}</a>
                </x-slot>
                {{ __('event.Detalles del Evento') }}
            </x-backend.section-header>

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('event.name') }}</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $event->name }}" readonly>
            </div>

            <div class="mb-3">
                <label for="tipo" class="form-label">{{ __('event.Tipo') }}</label>
                <input type="text" class="form-control" id="tipo" name="tipo" value="{{ $event->tipo }}" readonly>
            </div>

            <div class="mb-3">
                <label for="fecha" class="form-label">{{ __('event.Fecha') }}</label>
                <input type="text" class="form-control" id="fecha" name="fecha" value="{{ $event->fecha }}" readonly>
            </div>

            <div class="mb-3">
                <label for="hora" class="form-label">{{ __('event.Hora') }}</label>
                <input type="text" class="form-control" id="hora" name="hora" value="{{ $event->fecha }}" readonly>
            </div>

            <div class="mb-3">
                <label for="user_id" class="form-label">{{ __('event.Nombre del Organizador') }}</label>
                <input type="text" class="form-control" id="user_id" name="user_id" value="{{ $event->user->first_name }} {{ $event->user->last_name }}" readonly>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">{{ __('event.Descripción') }} ({{ __('Opcional') }})</label>
                <textarea class="form-control" id="description" name="description" readonly>{{ $event->description }}</textarea>
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">{{ __('event.location') }} ({{ __('Opcional') }})</label>
                <textarea class="form-control" id="location" name="location" readonly>{{ $event->location }}</textarea>
            </div>

            <div class="mt-4">
                <a href="{{ route('backend.events.edit', $event->id) }}" class="btn btn-primary">{{ __('event.Editar') }}</a>
                <a href="{{ route('backend.events.index') }}" class="btn btn-secondary">{{ __('event.Atras') }}</a>
            </div>
        </div>
    </div>
@endsection

@push('after-styles')
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush