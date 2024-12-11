<div class="d-flex gap-2 align-items-center">

@hasPermission('delete_review')
        <a href="{{route("backend.employees.destroy_review", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="fs-4 text-danger" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('rating.delete')}}" data-confirm="{{ __('messages.are_you_sure?') }}"> <i class="icon-delete"></i></a>
        @if ($data->status === 0)
        <form action="{{route('backend.rating.approve')}}" method="POST">
            @csrf
            <input type="hidden" name="module_name" value="{{$data->module}}">
            <input type="hidden" name="module_id" value="{{$data->id}}">
            <button type="submit" class="btn border-0 bg-transparent text-success" title="{{__('rating.approve')}}" ><i class="fas fa-check"></i></button>
        </form>
        @endif

@endhasPermission
</div>
