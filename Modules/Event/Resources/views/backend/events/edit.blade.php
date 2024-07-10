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
                    <a class="btn btn-primary" href="{{ route('backend.events.index') }}">{{ __('event.Atras') }}</a>
                </x-slot>
                {{ __('event.Editar') }}
            </x-backend.section-header>

            <form action="{{ route('backend.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="titulo" class="form-label">{{ __('event.titulo') }}</label>
                    <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo', $event->titulo) }}" required>
                    @error('titulo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="tipo" class="form-label">{{ __('event.Tipo') }}</label>
                    <select class="form-control @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                        <option value="salud" {{ old('tipo', $event->tipo) == 'salud' ? 'selected' : '' }}>{{ __('Salud') }}</option>
                        <option value="entrenamiento" {{ old('tipo', $event->tipo) == 'entrenamiento' ? 'selected' : '' }}>{{ __('Entrenamiento') }}</option>
                    </select>
                    @error('tipo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="fecha" class="form-label">{{ __('event.Fecha') }}</label>
                    <input type="date" class="form-control @error('fecha') is-invalid @enderror" id="fecha" name="fecha" value="{{ old('fecha', $event->fecha ? $event->fecha->format('Y-m-d') : '') }}" required>
                    @error('fecha')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="hora" class="form-label">{{ __('event.Hora') }}</label>
                    <input type="time" class="form-control @error('hora') is-invalid @enderror" id="hora" name="hora" value="{{ old('hora', $event->fecha ? $event->fecha->format('H:i') : '') }}" required>
                    @error('hora')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div id="calendar-container" class="mb-3">
                    <div id="calendar"></div>
                </div>
                <div class="mb-3">
                    <label for="user_id" class="form-label">{{ __('event.Nombre del Organizador') }}</label>
                    <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $event->user_id) == $user->id ? 'selected' : '' }}>{{ $user->first_name }} {{ $user->last_name }}</option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">{{ __('event.Descripción') }} ({{ __('Opcional') }})</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion">{{ old('descripcion', $event->descripcion) }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="ubication" class="form-label">{{ __('event.ubication') }} ({{ __('Opcional') }})</label>
                    <textarea class="form-control @error('ubication') is-invalid @enderror" id="ubication" name="ubication" placeholder="https://www.google.com/maps/...">{{ old('ubication', $event->ubication) }}</textarea>
                    @error('ubication')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-success">{{ __('event.Editar') }}</button>
                <a href="{{ route('backend.events.index') }}" class="btn btn-secondary">{{ __('event.Cancelar') }}</a>
            </form>
        </div>
    </div>
@endsection

@push('after-styles')
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
@endpush

@push('after-scripts')
<script src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var events = @json($events);

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: events,
            dateClick: function(info) {
                document.getElementById('fecha').value = info.dateStr;
            },
            eventContent: function(info) {
                return { html: info.event.title + ' ' + info.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }; // Mostrar el título y la hora del evento
            }
        });

        calendar.render();

        // Actualiza el calendario cuando se cambian los inputs de fecha y hora
        document.getElementById('fecha').addEventListener('change', function() {
            updateCalendarEvent();
        });

        document.getElementById('hora').addEventListener('change', function() {
            updateCalendarEvent();
        });

        function updateCalendarEvent() {
            var fecha = document.getElementById('fecha').value;
            var hora = document.getElementById('hora').value;
            if (fecha && hora) {
                var event = {
                    title: document.getElementById('titulo').value,
                    start: fecha + 'T' + hora + ':00',
                    allDay: false
                };
                calendar.addEvent(event);
            }
        }
    });
</script>
@endpush