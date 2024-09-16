@extends('backend.layouts.app')

@section('title') {{ __('Crear Antigarrapata') }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.mascotas.antigarrapatas.store', ['pet' => $pet]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="antigarrapata_name" class="form-label">{{ __('Nombre del Antigarrapata') }}</label>
                    <input type="text" class="form-control @error('antigarrapata_name') is-invalid @enderror" id="antigarrapata_name" name="antigarrapata_name" value="{{ old('antigarrapata_name') }}" required>
                    @error('antigarrapata_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="fecha_aplicacion" class="form-label">{{ __('Fecha de Aplicación') }}</label>
                    <input type="date" class="form-control @error('fecha_aplicacion') is-invalid @enderror" id="fecha_aplicacion" name="fecha_aplicacion" value="{{ old('fecha_aplicacion') }}" required>
                    @error('fecha_aplicacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="patron_refuerzo" class="form-label">{{ __('Patrón de Refuerzo') }}</label>
                    <div>
                        <input type="radio" name="patron_refuerzo" value="sin_patron" checked> {{ __('Sin Patrón') }}<br>
                        <input type="radio" name="patron_refuerzo" value="mensual"> {{ __('Mensual') }}<br>
                        <input type="radio" name="patron_refuerzo" value="trimestral"> {{ __('Trimestral') }}<br>
                        <input type="radio" name="patron_refuerzo" value="semestral"> {{ __('Semestral') }}
                    </div>
                </div>

                <div class="mb-3">
                    <label for="fecha_refuerzo_antigarrapata" class="form-label">{{ __('Fecha de Refuerzo Antigarrapata') }}</label>
                    <input type="date" class="form-control @error('fecha_refuerzo_antigarrapata') is-invalid @enderror" id="fecha_refuerzo_antigarrapata" name="fecha_refuerzo_antigarrapata" value="{{ old('fecha_refuerzo_antigarrapata') }}" required>
                    @error('fecha_refuerzo_antigarrapata')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">{{ __('Crear Antigarrapata') }}</button>
                <a href="{{ route('backend.mascotas.antigarrapatas.index', ['pet' => $pet]) }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const fechaAplicacionInput = document.getElementById('fecha_aplicacion');
        const fechaRefuerzoInput = document.getElementById('fecha_refuerzo_antigarrapata');
        const patronRefuerzoRadios = document.querySelectorAll('input[name="patron_refuerzo"]');

        function calcularFechaRefuerzo() {
            const fechaAplicacion = new Date(fechaAplicacionInput.value);
            let mesesAgregar = 0;

            patronRefuerzoRadios.forEach((radio) => {
                if (radio.checked) {
                    switch (radio.value) {
                        case 'mensual':
                            mesesAgregar = 1;
                            break;
                        case 'trimestral':
                            mesesAgregar = 3;
                            break;
                        case 'semestral':
                            mesesAgregar = 6;
                            break;
                        default:
                            mesesAgregar = 0;
                    }
                }
            });

            if (mesesAgregar > 0 && !isNaN(fechaAplicacion.getTime())) {
                fechaAplicacion.setMonth(fechaAplicacion.getMonth() + mesesAgregar);
                const nuevaFechaRefuerzo = fechaAplicacion.toISOString().split('T')[0];
                fechaRefuerzoInput.value = nuevaFechaRefuerzo;
            } else {
                fechaRefuerzoInput.value = '';
            }
        }

        // Escuchar cambios en la fecha de aplicación y en los radio buttons
        fechaAplicacionInput.addEventListener('change', calcularFechaRefuerzo);
        patronRefuerzoRadios.forEach((radio) => {
            radio.addEventListener('change', calcularFechaRefuerzo);
        });
    });
    </script>
@endsection
