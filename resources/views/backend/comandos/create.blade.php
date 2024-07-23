@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.comandos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('comando_entrenamiento.name') }}</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('comando_entrenamiento.Enter_name') }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('comando_entrenamiento.description') }}</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="{{ __('comando_entrenamiento.Enter_description') }}"></textarea>
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">{{ __('comando_entrenamiento.type') }}</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="especializado">{{ __('Especializado') }}</option>
                        <option value="basico">{{ __('Básico') }}</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="is_favorite" class="form-label">{{ __('comando_entrenamiento.is_favorite') }}</label>
                    <select class="form-control" id="is_favorite" name="is_favorite" required>
                        <option value="0">{{ __('No') }}</option>
                        <option value="1">{{ __('Sí') }}</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">{{ __('comando_entrenamiento.category') }}</label>
                    <div class="input-group">
                        <select class="form-control" id="category_id" name="category_id" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            {{ __('Añadir Categoría') }}
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="voz_comando" class="form-label">{{ __('comando_entrenamiento.voz_comando') }}</label>
                    <input type="text" class="form-control" id="voz_comando" name="voz_comando" placeholder="{{ __('comando_entrenamiento.Enter_voz_comando') }}" required>
                </div>

                <div class="mb-3">
                    <label for="instructions" class="form-label">{{ __('comando_entrenamiento.instructions') }}</label>
                    <textarea class="form-control" id="instructions" name="instructions" rows="5" placeholder="{{ __('comando_entrenamiento.Enter_instructions') }}" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('comando_entrenamiento.create') }}</button>
            </form>
        </div>
    </div>

    <!-- Modal para añadir categoría -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="addCategoryForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCategoryModalLabel">{{ __('Añadir Categoría') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_category_name" class="form-label">{{ __('Nombre de la Categoría') }}</label>
                            <input type="text" class="form-control" id="new_category_name" name="new_category_name" placeholder="{{ __('Ingrese el nombre de la categoría') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancelar') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Guardar Categoría') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('after-styles')
<link rel="stylesheet" href='{{ mix("modules/product/style.css") }}'>
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
<script src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
<script>
    document.getElementById('addCategoryForm').addEventListener('submit', function(event) {
        event.preventDefault();
        
        var newCategoryName = document.getElementById('new_category_name').value;
        
        // Enviar la nueva categoría al servidor
        fetch('{{ route("backend.categories_comando.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name: newCategoryName })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Añadir la nueva categoría al select
                var newOption = new Option(data.category.name, data.category.id, true, true);
                document.getElementById('category_id').append(newOption);
                // Cerrar el modal
                $('#addCategoryModal').modal('hide');
            } else {
                // Manejar errores
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });
</script>
@endpush