@extends('backend.layouts.app')

@section('title') {{ __('Editar Vacuna') }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.mascotas.vacunas.update', ['pet' => $pet->id, 'vacuna' => $vacuna->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="vacuna_name" class="form-label">{{ __('Nombre de la Vacuna') }}</label>
                    <input type="text" class="form-control @error('vacuna_name') is-invalid @enderror" id="vacuna_name" name="vacuna_name" value="{{ old('vacuna_name', $vacuna->vacuna_name) }}" required>
                    @error('vacuna_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="fecha_aplicacion" class="form-label">{{ __('Fecha de Aplicación') }}</label>
                    <input type="date" class="form-control @error('fecha_aplicacion') is-invalid @enderror" id="fecha_aplicacion" name="fecha_aplicacion" value="{{ old('fecha_aplicacion', $vacuna->fecha_aplicacion) }}" required>
                    @error('fecha_aplicacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="patron_refuerzo" class="form-label">{{ __('Patrón de Refuerzo') }}</label>
                    <div>
                        <input type="radio" name="patron_refuerzo" value="sin_patron" {{ old('patron_refuerzo') == 'sin_patron' || !$vacuna->patron_refuerzo ? 'checked' : '' }}> {{ __('Sin Patrón') }}<br>
                        <input type="radio" name="patron_refuerzo" value="mensual" {{ old('patron_refuerzo') == 'mensual' || $vacuna->patron_refuerzo == 'mensual' ? 'checked' : '' }}> {{ __('Mensual') }}<br>
                        <input type="radio" name="patron_refuerzo" value="trimestral" {{ old('patron_refuerzo') == 'trimestral' || $vacuna->patron_refuerzo == 'trimestral' ? 'checked' : '' }}> {{ __('Trimestral') }}<br>
                        <input type="radio" name="patron_refuerzo" value="semestral" {{ old('patron_refuerzo') == 'semestral' || $vacuna->patron_refuerzo == 'semestral' ? 'checked' : '' }}> {{ __('Semestral') }}
                    </div>
                </div>

                <div class="mb-3">
                    <label for="fecha_refuerzo_vacuna" class="form-label">{{ __('Fecha de Refuerzo') }}</label>
                    <input type="date" class="form-control @error('fecha_refuerzo_vacuna') is-invalid @enderror" id="fecha_refuerzo_vacuna" name="fecha_refuerzo_vacuna" value="{{ old('fecha_refuerzo_vacuna', $vacuna->fecha_refuerzo_vacuna) }}" required>
                    @error('fecha_refuerzo_vacuna')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">{{ __('Actualizar Vacuna') }}</button>
                <a href="{{ route('backend.mascotas.vacunas.index', ['pet' => $pet->id]) }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fechaAplicacionInput = document.getElementById('fecha_aplicacion');
            const fechaRefuerzoInput = document.getElementById('fecha_refuerzo_vacuna');
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
