<div class="d-flex gap-2 align-items-center">

    @hasPermission('delete_review')
        <form action="{{ route('backend.employees.destroy_review', $data->id) }}" method="POST" class="delete-form"
            style="display: inline;">
            @csrf
            @method('DELETE')
            <input type="hidden" name="module_name" value="{{$data->module}}">
            <button type="button" class="btn border-0 bg-transparent delete-button-rating text-danger" data-bs-toggle="tooltip"
                title="{{ __('rating.delete') }}" data-confirm="{{ __('messages.are_you_sure?') }}">
                <i class="icon-delete"></i>
            </button>
        </form>
        @if ($data->status === 0)
            <form action="{{ route('backend.rating.approve') }}" method="POST" class="approve-form-rating">
                @csrf
                <input type="hidden" name="module_name" value="{{ $data->module }}">
                <input type="hidden" name="module_id" value="{{ $data->id }}">
                <button type="submit" class="btn border-0 bg-transparent text-success"
                    title="{{ __('rating.approve') }}"><i class="fas fa-check"></i></button>
            </form>
        @endif
    @endhasPermission
</div>
