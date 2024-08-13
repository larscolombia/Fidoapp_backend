<div class="d-flex gap-2 align-items-center">
    <a href="{{ route('backend.chips.show', ['chip' => $data->id]) }}"
        class="btn btn-sm btn-icon btn-soft-warning" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('Ver detalles') }}">
        <i class="fa-solid fa-eye"></i>
    </a>
    <a href="{{ route('backend.chips.edit', ['chip' => $data->id]) }}"
        class="btn btn-sm btn-icon btn-soft-warning" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('Editar detalles') }}">
        <i class="fa-solid fa-edit"></i>
    </a>
    <button type="button" class="btn btn-sm btn-icon btn-soft-danger" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('Eliminar') }}" onclick="showDeleteModal({{ $data->id }})">
        <i class="fa-solid fa-trash"></i>
    </button>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">{{ __('Confirmar eliminación') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ __('¿Estás seguro de que quieres eliminar este chip?') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancelar') }}</button>
                <form id="deleteForm" action="" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('Eliminar') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showDeleteModal(id) {
        const form = document.getElementById('deleteForm');
        form.action = 'chips/' + id;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>