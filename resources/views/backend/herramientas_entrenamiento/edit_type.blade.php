@extends('backend.layouts.app')

@section('title') {{ __('Editar Ícono de Herramienta de Entrenamiento') }} @endsection

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
                    <a class="btn btn-primary" href="{{ route('backend.herramientas_entrenamiento.icon') }}">{{ __('Volver') }}</a>
                </x-slot>
                {{ __('Editar Ícono de Herramienta de Entrenamiento') }}
            </x-backend.section-header>

            <form action="{{ route('backend.herramientas_entrenamiento_type.update', $herramienta_entrenamiento_type->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="icon" class="form-label">{{ __('Ícono') }}</label>
                    <div class="input-group">
                        <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" placeholder="fa-example" value="{{ old('icon', $herramienta_entrenamiento_type->icon) }}" required readonly>
                        <button type="button" class="btn btn-secondary" id="iconPickerBtn">{{ __('Seleccionar Ícono') }}</button>
                    </div>
                    <small class="form-text text-muted">{{ __('Use FontAwesome class names (e.g., fa-dog, fa-cat)') }}</small>
                    @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">{{ __('Actualizar Ícono') }}</button>
                <a href="{{ route('backend.herramientas_entrenamiento.icon') }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
            </form>
        </div>
    </div>
@endsection

@push('after-styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/css/bootstrap-iconpicker.min.css">
@endpush

@push('after-scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/js/bootstrap-iconpicker.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#iconPickerBtn').iconpicker({
            iconset: 'fontawesome',
            icon: '{{ old('icon', $herramienta_entrenamiento_type->icon) }}',
            rows: 5,
            cols: 10,
            placement: 'bottom',
            align: 'left',
            arrowClass: 'btn-info',
            arrowPrevIconClass: 'fa fa-angle-left',
            arrowNextIconClass: 'fa fa-angle-right',
            footer: false,
            searchText: 'Buscar...',
            labelHeader: '{0} de {1} Pags.',
        }).on('change', function(e) {
            $('#icon').val(e.icon);
        });
    });
</script>
@endpush