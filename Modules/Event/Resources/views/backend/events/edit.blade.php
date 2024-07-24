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
                    <label for="name" class="form-label">{{ __('event.titulo') }}</label>
                    <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="name" name="name" value="{{ old('name', $event->name) }}" required>
                    @error('name')
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
                    <label for="date" class="form-label">{{ __('event.Fecha y Hora de Inicio') }}</label>
                    <input type="datetime-local" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $event->date ? \Carbon\Carbon::parse($event->date)->format('Y-m-d\TH:i') : '') }}" required>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="end_date" class="form-label">{{ __('event.Fecha y Hora de Fin') }}</label>
                    <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('Y-m-d\TH:i') : '') }}" required>
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div id="calendar-container" class="mb-3">
                    <div id="calendar"></div> <!-- Asegúrate de que este div esté presente -->
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
                    <label for="description" class="form-label">{{ __('event.Descripción') }} ({{ __('Opcional') }})</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $event->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">{{ __('event.ubication') }} ({{ __('Opcional') }})</label>
                    <textarea class="form-control @error('location') is-invalid @enderror" id="location" name="location" placeholder="https://www.google.com/maps/...">{{ old('location', $event->location) }}</textarea>
                    @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">{{ __('event.Imagen') }}</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                    @if ($event->image)
                        <img src="{{ asset($event->image) }}" class="img-fluid mt-3" style="max-width: 100%; max-height: 300px;" alt="{{ $event->name }}">
                    @endif
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-success">{{ __('event.Editar') }}</button>
                <a href="{{ route('backend.events.index') }}" class="btn btn-secondary">{{ __('event.Cancelar') }}</a>
                <button class="btn btn-primary">Agregar a Google Calendar</button>
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
                    document.getElementById('date').value = info.dateStr;
                },
                eventContent: function(info) {
                    return { 
                        html: info.event.title + ' ' + 
                        info.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) + ' - ' +
                        (info.event.end ? info.event.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '')
                    };
                }
            });

            calendar.render();

            // Function to update event on calendar
            function updateCalendarEvent() {
                var fecha = document.getElementById('date').value;
                var endDate = document.getElementById('end_date').value;
                var eventId = "{{ $event->id }}"; // Ensure event ID is available in the view
                
                var event = calendar.getEventById(eventId); // Get the event by ID
                
                if (event) {
                    event.setDates(fecha, endDate); // Update the event dates
                } else {
                    // If the event does not exist, create it (if necessary)
                    event = {
                        id: eventId,
                        title: document.getElementById('name').value,
                        start: fecha,
                        end: endDate,
                        allDay: false
                    };
                    calendar.addEvent(event);
                }
            }

            // Attach change events to date and end_date fields
            document.getElementById('date').addEventListener('change', updateCalendarEvent);
            document.getElementById('end_date').addEventListener('change', updateCalendarEvent);
        });
    </script>
@endpush