<div class="d-flex gap-2 align-items-center">
    <a href="{{ route('backend.course_platform.clases.show', ['course' => request()->route('course'), 'clase' => $data->id]) }}"
        class="btn btn-sm btn-icon btn-soft-warning" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('clases.View Details') }}">
        <i class="fa-solid fa-eye"></i>
    </a>
    <a href="{{ route('backend.course_platform.clases.edit', ['course' => request()->route('course'), 'clase' => $data->id]) }}"
        class="btn btn-sm btn-icon btn-soft-warning" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('clases.Edit Details') }}">
        <i class="fa-solid fa-edit"></i>
    </a>
    <button type="button" class="btn btn-sm btn-icon btn-soft-danger" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('clases.Delete') }}" onclick="showDeleteModal({{ $data->id }}, {{$data->course_platform_id}})">
        <i class="fa-solid fa-trash"></i>
    </button>
    {{-- <a class="btn btn-primary btn-sm" href="{{ route('backend.clases.ejercicios.index', ['clase' => $data->id]) }}">{{ __('clases.show_exercises') }}</a> --}}
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">{{ __('clases.Confirm Deletion') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ __('clases.Are you sure you want to delete this class?') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('clases.cancel') }}</button>
                <form id="deleteForm" action="{{route('backend.course_platform.clases.destroy',[$data->course_platform_id, $data->id])}}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('clases.Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showDeleteModal(id, cursoId) {
        const form = document.getElementById('deleteForm');
        form.action = "{{ url('app/curso-plataforma') }}/" + cursoId + "/clases/" + id;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
