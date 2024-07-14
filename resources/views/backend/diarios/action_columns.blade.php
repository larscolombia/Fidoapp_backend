<div class="d-flex gap-2 align-items-center">
    <a href="{{ route('backend.mascotas.diarios.show', ['pet' => $data->pet->id, 'diario' => $data->id]) }}"
        class="btn btn-sm btn-icon btn-soft-warning" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('diarios.View Details') }}">
        <i class="fa-solid fa-eye"></i>
    </a>
    <a href="{{ route('backend.mascotas.diarios.edit', ['pet' => $data->pet->id, 'diario' => $data->id]) }}"
        class="btn btn-sm btn-icon btn-soft-warning" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('diarios.Edit Details') }}">
        <i class="fa-solid fa-edit"></i>
    </a>
    <button type="button" class="btn btn-sm btn-icon btn-soft-danger" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('diarios.Delete Details') }}" onclick="showDeleteModal({{ $data->id }})">
        <i class="fa-solid fa-trash"></i>
    </button>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">{{ __('diarios.Confirm Deletion') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ __('diarios.Are you sure you want to delete this diario?') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('diarios.Cancel') }}</button>
                <form id="deleteForm" action="" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('diarios.Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showDeleteModal(id) {
        const form = document.getElementById('deleteForm');
        form.action = "{{ __('backend.mascotas.diarios.destroy', ['pet' => $data->pet->id, 'diario' => $data->id]) }}";
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>