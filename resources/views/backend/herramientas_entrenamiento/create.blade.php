@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.herramientas_entrenamiento.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('herramientas_entrenamiento.Nombre') }}</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Ingrese el nombre') }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('herramientas_entrenamiento.Descripción') }}</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="{{ __('Ingrese la descripción') }}"></textarea>
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">{{ __('herramientas_entrenamiento.Tipo') }}</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="clicker">{{ __('herramientas_entrenamiento.Clicker') }}</option>
                        <option value="silbato">{{ __('herramientas_entrenamiento.Silbato') }}</option>
                        <option value="diarios">{{ __('herramientas_entrenamiento.Diarios') }}</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">{{ __('herramientas_entrenamiento.status') }}</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="active">{{ __('herramientas_entrenamiento.Activo') }}</option>
                        <option value="inactive">{{ __('herramientas_entrenamiento.Inactivo') }}</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="audio" class="form-label">{{ __('herramientas_entrenamiento.Audio') }}</label>
                    <input type="file" class="form-control" id="audio" name="audio" accept="audio/*" required>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('herramientas_entrenamiento.Crear Herramienta') }}</button>
            </form>
        </div>
    </div>
@endsection

@push('after-styles')
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush