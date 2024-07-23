@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.comandos.update', $comando->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('comando_entrenamiento.name') }}</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $comando->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('comando_entrenamiento.description') }}</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $comando->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">{{ __('comando_entrenamiento.type') }}</label>
                    <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                        <option value="especializado" {{ old('type', $comando->type) == 'especializado' ? 'selected' : '' }}>{{ __('comando_entrenamiento.especializado') }}</option>
                        <option value="basico" {{ old('type', $comando->type) == 'basico' ? 'selected' : '' }}>{{ __('comando_entrenamiento.basico') }}</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="is_favorite" class="form-label">{{ __('comando_entrenamiento.is_favorite') }}</label>
                    <select class="form-control @error('is_favorite') is-invalid @enderror" id="is_favorite" name="is_favorite" required>
                        <option value="1" {{ old('is_favorite', $comando->is_favorite) == '1' ? 'selected' : '' }}>{{ __('Sí') }}</option>
                        <option value="0" {{ old('is_favorite', $comando->is_favorite) == '0' ? 'selected' : '' }}>{{ __('No') }}</option>
                    </select>
                    @error('is_favorite')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">{{ __('comando_entrenamiento.category') }}</label>
                    <div class="d-flex">
                        <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $comando->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">{{ __('comando_entrenamiento.add_category') }}</button>
                    </div>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="voz_comando" class="form-label">{{ __('comando_entrenamiento.voz_comando') }}</label>
                    <input type="text" class="form-control @error('voz_comando') is-invalid @enderror" id="voz_comando" name="voz_comando" value="{{ old('voz_comando', $comando->voz_comando) }}" required>
                    @error('voz_comando')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="instructions" class="form-label">{{ __('comando_entrenamiento.instructions') }}</label>
                    <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions" rows="3" required>{{ old('instructions', $comando->instructions) }}</textarea>
                    @error('instructions')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">{{ __('comando_entrenamiento.update') }}</button>
            </form>
        </div>
    </div>

    <!-- Modal para añadir categoría -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">{{ __('comando_entrenamiento.add_category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm">
                        @csrf
                        <div class="mb-3">
                            <label for="category_name" class="form-label">{{ __('comando_entrenamiento.category_name') }}</label>
                            <input type="text" class="form-control" id="category_name" name="name" required>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('comando_entrenamiento.add') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
<script>
    document.getElementById('addCategoryForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);

        fetch('{{ route('backend.categories.store') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const categorySelect = document.getElementById('category_id');
                const newOption = document.createElement('option');
                newOption.value = data.category.id;
                newOption.text = data.category.name;
                categorySelect.add(newOption);
                categorySelect.value = data.category.id;
                $('#addCategoryModal').modal('hide');
            } else {
                alert(data.message);
            }
        });
    });
</script>
@endpush