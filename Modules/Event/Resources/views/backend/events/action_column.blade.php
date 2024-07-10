<div class="d-flex gap-2 align-items-center">
    @can('edit_'. $module_name)
        <a href="{{ route('backend.events.edit', ['event' => $data->id]) }}" type="button" class="fs-4 text-primary border-0 bg-transparent"  title="{{__('Edit')}} " > 
            <i class="icon-Edit"></i>
        </a>
    @endcan
    @can('edit_'. $module_name)
        <a href="{{ route('backend.events.show', ['event' => $data->id]) }}" type="button" class="fs-4 text-primary border-0 bg-transparent"  title="{{__('event.Show')}} " > 
            <i class="icon-Show"></i>
        </a>
    @endcan
    @can('delete_'.$module_name)
        <a href="{{route("backend.$module_name.destroy", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="fs-4 text-danger" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{ __('messages.are_you_sure?') }}"> <i class="icon-delete"></i></a>
    @endcan
</div>

