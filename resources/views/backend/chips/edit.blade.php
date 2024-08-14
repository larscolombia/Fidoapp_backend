@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.chips.update', $chip->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="num_identificacion" class="form-label">{{ __('chips.chip_identification_number') }}</label>
                    <input type="text" class="form-control @error('num_identificacion') is-invalid @enderror" id="num_identificacion" name="num_identificacion" value="{{ old('num_identificacion', $chip->num_identificacion) }}" required>
                    @error('num_identificacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="pet_name" class="form-label">{{ __('chips.pet_name') }}</label>
                    <input type="text" class="form-control" id="pet_name" name="pet_name" value="{{ $chip->pet->name }}" readonly>
                    <input type="hidden" id="pet_id" name="pet_id" value="{{ $chip->pet_id }}">
                </div>

                <div class="mb-3">
                    <label for="fecha_implantacion" class="form-label">{{ __('chips.installation_date') }}</label>
                    <input type="date" class="form-control @error('fecha_implantacion') is-invalid @enderror" id="fecha_implantacion" name="fecha_implantacion" value="{{ old('fecha_implantacion', $chip->fecha_implantacion->format('Y-m-d')) }}" required>
                    @error('fecha_implantacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="fabricante_id" class="form-label">{{ __('chips.manufacturer') }}</label>
                    <div class="input-group">
                        <select class="form-control @error('fabricante_id') is-invalid @enderror" id="fabricante_id" name="fabricante_id" required>
                            @foreach($fabricantes as $fabricante)
                                <option value="{{ $fabricante->id }}" {{ $chip->fabricante_id == $fabricante->id ? 'selected' : '' }}>
                                    {{ $fabricante->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addFabricanteModal">
                            {{ __('chips.add_manufacturer') }}
                        </button>
                        @error('fabricante_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="num_contacto" class="form-label">{{ __('chips.contact_number') }}</label>
                    <input type="text" class="form-control @error('num_contacto') is-invalid @enderror" id="num_contacto" name="num_contacto" value="{{ old('num_contacto', $chip->num_contacto) }}" required>
                    @error('num_contacto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">{{ __('chips.update_chip') }}</button>
            </form>
        </div>
    </div>

    <!-- Modal para añadir fabricante -->
    <div class="modal fade" id="addFabricanteModal" tabindex="-1" aria-labelledby="addFabricanteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="addFabricanteForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFabricanteModalLabel">{{ __('chips.add_manufacturer') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_fabricante_name" class="form-label">{{ __('chips.manufacturer_name') }}</label>
                            <input type="text" class="form-control" id="new_fabricante_name" name="new_fabricante_name" placeholder="{{ __('chips.enter_manufacturer_name') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('chips.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('chips.save_manufacturer') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('after-styles')
<link rel="stylesheet" href='{{ mix("modules/product/style.css") }}'>
@endpush

@push('after-scripts')
<script>
    document.getElementById('addFabricanteForm').addEventListener('submit', function(event) {
        event.preventDefault();
        
        var newFabricanteName = document.getElementById('new_fabricante_name').value;
        
        // Enviar el nuevo fabricante al servidor
        fetch('{{ route("backend.fabricantes.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ nombre: newFabricanteName })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Añadir el nuevo fabricante al select
                var newOption = new Option(data.fabricante.nombre, data.fabricante.id, true, true);
                document.getElementById('fabricante_id').append(newOption);
                // Cerrar el modal
                $('#addFabricanteModal').modal('hide');
            } else {
                // Manejar errores
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });
</script>
@endpush