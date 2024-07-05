<div class="d-flex gap-2 align-items-center">
    <a href="{{ route('backend.courses.show', ['course' => $data->id]) }}"
        class="btn btn-sm btn-icon btn-soft-warning" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('courses.View Details') }}">
        <i class="fa-solid fa-eye"></i>
    </a>
    <a href="{{ route('backend.courses.edit', ['course' => $data->id]) }}"
        class="btn btn-sm btn-icon btn-soft-warning" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('courses.Edit Details') }}">
        <i class="fa-solid fa-edit"></i>
    </a>
    <button type="button" class="btn btn-sm btn-icon btn-soft-danger" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('courses.Delete') }}" onclick="showDeleteModal({{ $data->id }})">
        <i class="fa-solid fa-trash"></i>
    </button>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">{{ __('courses.Confirm Deletion') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ __('courses.Are you sure you want to delete this course?') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('courses.Cancel') }}</button>
                <form id="deleteForm" action="" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('courses.Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showDeleteModal(id) {
        const form = document.getElementById('deleteForm');
        form.action = 'courses/' + id;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>