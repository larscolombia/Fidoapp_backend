@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.chips.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="num_identificacion" class="form-label">{{ __('chips.chip_identification_number') }}</label>
                    <input type="text" class="form-control" id="num_identificacion" name="num_identificacion" placeholder="{{ __('chips.enter_chip_identification_number') }}" required>
                </div>

                <div class="mb-3">
                    <label for="pet_name" class="form-label">{{ __('chips.pet_name') }}</label>
                    <input type="text" class="form-control" id="pet_name" name="pet_name" value="{{ $pet->name }}" readonly>
                    <input type="hidden" id="pet_id" name="pet_id" value="{{ $pet->id }}">
                </div>

                <div class="mb-3">
                    <label for="fecha_implantacion" class="form-label">{{ __('chips.installation_date') }}</label>
                    <input type="date" class="form-control" id="fecha_implantacion" name="fecha_implantacion" required>
                </div>

                <div class="mb-3">
                    <label for="fabricante_id" class="form-label">{{ __('chips.manufacturer') }}</label>
                    <div class="input-group">
                        <select class="form-control" id="fabricante_id" name="fabricante_id" required>
                            @foreach($fabricantes as $fabricante)
                                <option value="{{ $fabricante->id }}">{{ $fabricante->nombre }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addFabricanteModal">
                            {{ __('chips.add_manufacturer') }}
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="num_contacto" class="form-label">{{ __('chips.contact_number') }}</label>
                    <input type="text" class="form-control" id="num_contacto" name="num_contacto" placeholder="{{ __('chips.enter_contact_number') }}" required>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('chips.create_chip') }}</button>
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