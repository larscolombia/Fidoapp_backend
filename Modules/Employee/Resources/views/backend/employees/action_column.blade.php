<div class="d-flex gap-2 align-items-center">

    @hasPermission('edit_employee_password')
        <button type='button' data-assign-module="{{ $data->id }}" data-assign-target='#Employee_change_password'
            data-assign-event='employee_assign' class='fs-4 text-success border-0 bg-transparent text-nowrap'
            data-bs-toggle="tooltip" title="{{ __('messages.change_password') }}"><i class="icon-password"></i></button>
    @endhasPermission
    @hasPermission('edit_employees')
        <button type="button" class="fs-4 text-primary border-0 bg-transparent" data-crud-id="{{ $data->id }}"
            title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="icon-Edit"></i></button>
    @endhasPermission
    @hasPermission('delete_employees')
        <a href="{{ route("backend.$module_name.destroy", $data->id) }}" id="delete-{{ $module_name }}-{{ $data->id }}"
            class="fs-4 text-danger" data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}"
            data-bs-toggle="tooltip" title="{{ __('messages.delete') }}" data-confirm="{{ __('messages.are_you_sure?') }}">
            <i class="icon-delete"></i></a>
    @endhasPermission
</div>
