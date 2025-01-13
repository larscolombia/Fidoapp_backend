@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title mb-0">
                            {{ __('messages.permission_roles') }}
                        </h4>
                    </div>
                    <div>
                        <x-backend.section-header>
                            <div>

                            </div>
                            <x-slot name="toolbar">


                                <div class="input-group flex-nowrap">
                                </div>

                                @hasPermission('add_page')
                                    <x-buttons.offcanvas target='#form-offcanvas' class=" d-flex align-items-center gap-1"
                                        title="{{ __('Create') }} {{ __('page.lbl_role') }}">{{ __('messages.new') }}
                                    </x-buttons.offcanvas>
                                @endhasPermission
                            </x-slot>
                        </x-backend.section-header>


                    </div>
                </div>
                <div class="card-body">
                    @foreach ($roles as $role)
                        @if ($role->name !== 'admin')
                            {{ Form::open(['route' => ['backend.permission-role.store', $role->id], 'method' => 'post']) }}

                            <div class="permission-collapse border rounded p-3 mb-3" id="permission_{{ $role->id }}">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h6>{{ __('employee.' . $role->name) }}</h6>
                                    <div class="toggle-btn-groups">
                                        @if ($role->is_fixed == 0)
                                            <button class="btn btn-danger" type="button"
                                                onclick="delete_role({{ $role->id }})">
                                                Eliminar
                                            </button>
                                        @endif
                                        <button class="btn btn-gray ms-2" type="button"
                                            onclick="reset_permission({{ $role->id }})">
                                            {{ __('messages.default_permission') }}
                                        </button>
                                        <button class="btn btn-primary ms-2" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseBox1_{{ $role->id }}" aria-expanded="false"
                                            aria-controls="collapseExample_{{ $role->id }}">
                                            {{ __('messages.permission') }}
                                        </button>
                                    </div>
                                </div>
                                <div class="collapse pt-3" id="collapseBox1_{{ $role->id }}">
                                    <div class="table-responsive">
                                        <table class="table table-condensed table-striped mb-0">
                                            <thead class="sticky-top">
                                                <tr>
                                                    <th>{{ __('menu.modules') }}</th>
                                                    <th>{{ __('messages.view') }}</th>
                                                    <th>{{ __('messages.add') }}</th>
                                                    <th>{{ __('messages.edit') }}</th>
                                                    <th>{{ __('messages.delete') }} </th>
                                                    <th class="text-end">
                                                        {{ Form::submit(__('messages.save'), ['class' => 'btn btn-md btn-secondary']) }}
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($modules as $module)
                                                @php
                                                    $moduleName = ucwords($module->module_name);
                                                    if($moduleName == 'Booking'){
                                                        $moduleName = 'Reservas';
                                                    }
                                                    if($moduleName == 'Boarding'){
                                                        $moduleName = 'Alojamiento';
                                                    }
                                                    if($moduleName == 'Veterinary'){
                                                        $moduleName = 'Veterinario';
                                                    }
                                                    if($moduleName == 'Grooming'){
                                                        $moduleName = 'Cuidador';
                                                    }
                                                    if($moduleName == 'Traning'){
                                                        $moduleName = 'Entrenamiento';
                                                    }
                                                    if($moduleName == 'Walking'){
                                                        $moduleName = 'Paseador';
                                                    }
                                                    if($moduleName == 'DayCare'){
                                                        $moduleName = 'Guardería';
                                                    }
                                                    if($moduleName == 'PetSitter'){
                                                        $moduleName = 'Cuidador de mascotas';
                                                    }
                                                    if($moduleName == 'Service'){
                                                        $moduleName = 'Servicio';
                                                    }
                                                    if($moduleName == 'Category'){
                                                        $moduleName = 'Categoría';
                                                    }
                                                    if($moduleName == 'Subcategory'){
                                                        $moduleName = 'Subcategoría';
                                                    }
                                                    if($moduleName == 'Product'){
                                                        $moduleName = 'Producto';
                                                    }
                                                    if($moduleName == 'Product Variation'){
                                                        $moduleName = 'Variación de productos';
                                                    }

                                                    if($moduleName == 'Order'){
                                                        $moduleName = 'Orden';
                                                    }
                                                    if($moduleName == 'Supply'){
                                                        $moduleName = 'Suministro';
                                                    }
                                                    if($moduleName == 'Location'){
                                                        $moduleName = 'Ubicación';
                                                    }
                                                    if($moduleName == 'Employees'){
                                                        $moduleName = 'Profesionales';
                                                    }
                                                    if($moduleName == 'Owners'){
                                                        $moduleName = 'Propietarios';
                                                    }
                                                    if($moduleName == 'Review'){
                                                        $moduleName = 'Reseñas';
                                                    }
                                                    if($moduleName == 'Tax'){
                                                        $moduleName = 'Impuesto';
                                                    }
                                                    if($moduleName == 'Events'){
                                                        $moduleName = 'Eventos';
                                                    }
                                                    if($moduleName == 'Syetem Service'){
                                                        $moduleName = 'Sistema de servicios';
                                                    }
                                                    if($moduleName == 'Pet'){
                                                        $moduleName = 'Mascotas';
                                                    }
                                                    if($moduleName == 'Reports'){
                                                        $moduleName = 'Reportes';
                                                    }
                                                    if($moduleName == 'Constant'){
                                                        $moduleName = 'Constante';
                                                    }
                                                    if($moduleName == 'Page'){
                                                        $moduleName = 'Páginas';
                                                    }
                                                    if($moduleName == 'Notification'){
                                                        $moduleName = 'Notificación';
                                                    }
                                                    if($moduleName == 'App Banner'){
                                                        $moduleName = 'Aplicación Banner';
                                                    }
                                                    if($moduleName == 'Notification Template'){
                                                        $moduleName = 'Plantilla de notificación';
                                                    }
                                                    if($moduleName == 'Permission'){
                                                        $moduleName = 'Permisos';
                                                    }
                                                    if($moduleName == 'Modules'){
                                                        $moduleName = 'Módulos';
                                                    }
                                                    if($moduleName == 'Daily Bookings '){
                                                        $moduleName = 'Reservas diarias ';
                                                    }
                                                    if($moduleName == 'Overall Bookings'){
                                                        $moduleName = 'Reservas totales';
                                                    }
                                                    if($moduleName == 'Order Reports '){
                                                        $moduleName = 'Informes de órdenes';
                                                    }
                                                    if($moduleName == 'Pet Type '){
                                                        $moduleName = 'Tipo de mascotas';
                                                    }
                                                    if($moduleName == 'Breed'){
                                                        $moduleName = 'Raza';
                                                    }
                                                    if($moduleName == "Owner's Pet"){
                                                        $moduleName = 'Propietario de mascota';
                                                    }
                                                    if($moduleName == 'User Password'){
                                                        $moduleName = 'Contraseña de usuario';
                                                    }
                                                @endphp
                                                    <tr>
                                                        <td>{{ $moduleName }}</td>
                                                        <td>
                                                            <span><input type="checkbox"
                                                                    id="role-{{ $role->name }}-permission-view_{{ strtolower(str_replace(' ', '_', $module->module_name)) }}"
                                                                    name="permission[view_{{ strtolower(str_replace(' ', '_', $module->module_name)) }}][]"
                                                                    value="{{ $role->name }}" class="form-check-input"
                                                                    {{ AuthHelper::checkRolePermission($role, 'view_' . strtolower(str_replace(' ', '_', $module->module_name))) ? 'checked' : '' }}></span>

                                                        </td>
                                                        <td>
                                                            <span><input type="checkbox"
                                                                    id="role-{{ $role->name }}-permission-add_{{ strtolower(str_replace(' ', '_', $module->module_name)) }}"
                                                                    name="permission[add_{{ strtolower(str_replace(' ', '_', $module->module_name)) }}][]"
                                                                    value="{{ $role->name }}" class="form-check-input"
                                                                    {{ AuthHelper::checkRolePermission($role, 'add_' . strtolower(str_replace(' ', '_', $module->module_name))) ? 'checked' : '' }}></span>

                                                        </td>
                                                        <td>
                                                            <span><input type="checkbox"
                                                                    id="role-{{ $role->name }}-permission-edit_{{ strtolower(str_replace(' ', '_', $module->module_name)) }}"
                                                                    name="permission[edit_{{ strtolower(str_replace(' ', '_', $module->module_name)) }}][]"
                                                                    value="{{ $role->name }}" class="form-check-input"
                                                                    {{ AuthHelper::checkRolePermission($role, 'edit_' . strtolower(str_replace(' ', '_', str_replace(' ', '_', $module->module_name)))) ? 'checked' : '' }}></span>

                                                        </td>
                                                        <td>
                                                            <span><input type="checkbox"
                                                                    id="role-{{ $role->name }}-permission-delete_{{ strtolower(str_replace(' ', '_', $module->module_name)) }}"
                                                                    name="permission[delete_{{ strtolower(str_replace(' ', '_', $module->module_name)) }}][]"
                                                                    value="{{ $role->name }}" class="form-check-input"
                                                                    {{ AuthHelper::checkRolePermission($role, 'delete_' . strtolower(str_replace(' ', '_', $module->module_name))) ? 'checked' : '' }}></span>

                                                        </td>

                                                        @php
                                                            $decodedData = json_decode($module->more_permission, true);
                                                        @endphp

                                                        @if (!empty($decodedData))
                                                            <td class="text-end">

                                                                <a data-bs-toggle="collapse"
                                                                    data-bs-target="#demo_{{ $module->id }}"
                                                                    class="accordion-toggle  btn btn-primary btn-xs"><i
                                                                        class="fa-solid fa-chevron-down me-2">
                                                                    </i>{{ __('messages.more') }}</a>
                                                            </td>
                                                        @else
                                                            <td>

                                                            </td>
                                                        @endif
                                                    </tr>

                                                    <tr>
                                                        <td colspan="6" class="hiddenRow">
                                                            <div class="accordian-body collapse"
                                                                id="demo_{{ $module->id }}">
                                                                <table class="table table-striped mb-0">
                                                                    <tbody>
                                                                        @if ($decodedData != '')
                                                                            @foreach ($decodedData as $permission_data)
                                                                                <tr>
                                                                                    @php
                                                                                        $roleName = ucwords(str_replace('_', ' ', $permission_data));
                                                                                        if($roleName == 'Daily Bookings'){
                                                                                            $roleName = 'Reservas diarias';
                                                                                        }
                                                                                        if($roleName == 'Overall Bookings'){
                                                                                            $roleName = 'Reservas totales';
                                                                                        }
                                                                                        if($roleName == 'Order Reports'){
                                                                                            $roleName = 'Informes de órdenes';
                                                                                        }
                                                                                        if($roleName == 'Pet Type'){
                                                                                            $roleName = 'Tipo de mascota';
                                                                                        }
                                                                                        if($roleName == 'Breed'){
                                                                                            $roleName = 'Raza';
                                                                                        }
                                                                                        if($roleName == "Owner's Pet"){
                                                                                            $roleName = 'Propietario de mascota';
                                                                                        }
                                                                                        if($roleName == 'User Password'){
                                                                                            $roleName = 'Contraseña de usuario ';
                                                                                        }
                                                                                        if($roleName == 'Employee Password'){
                                                                                            $roleName = 'Contraseña de profesional';
                                                                                        }
                                                                                        if($roleName == 'Employee Earning'){
                                                                                            $roleName = 'Ganancias de los profesionales';
                                                                                        }
                                                                                        if($roleName == 'Employee Payout'){
                                                                                            $roleName = 'Pago a los profesionales';
                                                                                        }
                                                                                        if($roleName == 'Pending Employees'){
                                                                                            $roleName = 'Profesionales pendientes';
                                                                                        }
                                                                                        if($roleName == 'Pending Employees'){
                                                                                            $roleName = 'Profesionales pendientes';
                                                                                        }
                                                                                        if($roleName == 'City'){
                                                                                            $roleName = 'Ciudad';
                                                                                        }
                                                                                        if($roleName == 'State'){
                                                                                            $roleName = 'Estado';
                                                                                        }
                                                                                        if($roleName == 'Country'){
                                                                                            $roleName = 'País';
                                                                                        }
                                                                                        if($roleName == 'Logistics'){
                                                                                            $roleName = 'Logística';
                                                                                        }
                                                                                        if($roleName == 'Shipping Zones'){
                                                                                            $roleName = 'Zonas de expedición';
                                                                                        }
                                                                                        if($roleName == 'Brand'){
                                                                                            $roleName = 'Marca';
                                                                                        }
                                                                                        if($roleName == 'Product Category'){
                                                                                            $roleName = 'Categoría de productos';
                                                                                        }
                                                                                        if($roleName == 'Product Subcategory'){
                                                                                            $roleName = 'Subcategoría de productos';
                                                                                        }
                                                                                        if($roleName == 'Unit'){
                                                                                            $roleName = 'Unidad';
                                                                                        }
                                                                                        if($roleName == 'Tag'){
                                                                                            $roleName = 'Etiqueta';
                                                                                        }
                                                                                        if($roleName == 'Assign Service'){
                                                                                            $roleName = 'Asignar servicio';
                                                                                        }
                                                                                        if($roleName == 'Daycare Booking'){
                                                                                            $roleName = 'Reserva de guardería';
                                                                                        }
                                                                                        if($roleName == 'Care Taker'){
                                                                                            $roleName = 'Cuidador';
                                                                                        }
                                                                                        if($roleName == 'Walking Booking'){
                                                                                            $roleName = 'Reserva a paseador';
                                                                                        }
                                                                                        if($roleName == 'Walker'){
                                                                                            $roleName = 'Paseador';
                                                                                        }
                                                                                        if($roleName == 'Walking Duration'){
                                                                                            $roleName = 'Duración de la caminata';
                                                                                        }
                                                                                        if($roleName == 'Booking Request'){
                                                                                            $roleName = 'Solicitud de reserva';
                                                                                        }
                                                                                        if($roleName == 'Training Booking'){
                                                                                            $roleName = 'Reserva de entrenamiento';
                                                                                        }
                                                                                        if($roleName == 'Training Booking'){
                                                                                            $roleName = 'Reserva de entrenamiento';
                                                                                        }
                                                                                        if($roleName == 'Trainer'){
                                                                                            $roleName = 'Entrenamiento';
                                                                                        }
                                                                                        if($roleName == 'Training Type'){
                                                                                            $roleName = 'Tipo de entrenamiento';
                                                                                        }
                                                                                        if($roleName == 'Training Duration'){
                                                                                            $roleName = 'Duración del entrenamiento';
                                                                                        }
                                                                                        if($roleName == 'Grooming Booking'){
                                                                                            $roleName = 'Reserva de peluquería';
                                                                                        }
                                                                                        if($roleName == 'Groomer'){
                                                                                            $roleName = 'Peluquería';
                                                                                        }
                                                                                        if($roleName == 'Grooming Category'){
                                                                                            $roleName = 'Categoría de peluquería';
                                                                                        }
                                                                                        if($roleName == 'Grooming Service'){
                                                                                            $roleName = 'Servicio de peluquería';
                                                                                        }
                                                                                        if($roleName == 'Veterinary Booking'){
                                                                                            $roleName = 'Reservas veterinarias';
                                                                                        }
                                                                                        if($roleName == 'Veterinarian'){
                                                                                            $roleName = 'Veterinarios';
                                                                                        }
                                                                                        if($roleName == 'Veterinary Category'){
                                                                                            $roleName = 'Categoría Veterinaria';
                                                                                        }
                                                                                        if($roleName == 'Veterinary Service'){
                                                                                            $roleName = 'Servicio de veterinarios';
                                                                                        }
                                                                                        if($roleName == 'Boarding Booking'){
                                                                                            $roleName = 'Reserva de Alojamiento';
                                                                                        }
                                                                                        if($roleName == 'Boarder'){
                                                                                            $roleName = 'Alojamiento';
                                                                                        }
                                                                                        if($roleName == 'Facility'){
                                                                                            $roleName = 'Instalación';
                                                                                        }
                                                                                    @endphp
                                                                                    <td class="">

                                                                                        {{$roleName }}

                                                                                    </td>

                                                                                    <td>
                                                                                        <span><input type="checkbox"
                                                                                                id="role-{{ $role->name }}-permission-view_{{ strtolower(str_replace(' ', '_', $permission_data)) }}"
                                                                                                name="permission[view_{{ strtolower(str_replace(' ', '_', $permission_data)) }}][]"
                                                                                                value="{{ $role->name }}"
                                                                                                class="form-check-input"
                                                                                                {{ AuthHelper::checkRolePermission($role, 'view_' . strtolower(str_replace(' ', '_', $permission_data))) ? 'checked' : '' }}></span>

                                                                                    </td>
                                                                                    <td>
                                                                                        <span><input type="checkbox"
                                                                                                id="role-{{ $role->name }}-permission-add_{{ strtolower(str_replace(' ', '_', $permission_data)) }}"
                                                                                                name="permission[add_{{ strtolower(str_replace(' ', '_', $permission_data)) }}][]"
                                                                                                value="{{ $role->name }}"
                                                                                                class="form-check-input"
                                                                                                {{ AuthHelper::checkRolePermission($role, 'add_' . strtolower(str_replace(' ', '_', $permission_data))) ? 'checked' : '' }}></span>

                                                                                    </td>
                                                                                    <td>
                                                                                        <span><input type="checkbox"
                                                                                                id="role-{{ $role->name }}-permission-edit_{{ strtolower(str_replace(' ', '_', $permission_data)) }}"
                                                                                                name="permission[edit_{{ strtolower(str_replace(' ', '_', $permission_data)) }}][]"
                                                                                                value="{{ $role->name }}"
                                                                                                class="form-check-input"
                                                                                                {{ AuthHelper::checkRolePermission($role, 'edit_' . strtolower(str_replace(' ', '_', str_replace(' ', '_', $permission_data)))) ? 'checked' : '' }}></span>

                                                                                    </td>
                                                                                    <td>
                                                                                        <span><input type="checkbox"
                                                                                                id="role-{{ $role->name }}-permission-delete_{{ strtolower(str_replace(' ', '_', $permission_data)) }}"
                                                                                                name="permission[delete_{{ strtolower(str_replace(' ', '_', $permission_data)) }}][]"
                                                                                                value="{{ $role->name }}"
                                                                                                class="form-check-input"
                                                                                                {{ AuthHelper::checkRolePermission($role, 'delete_' . strtolower(str_replace(' ', '_', $permission_data))) ? 'checked' : '' }}></span>

                                                                                    </td>


                                                                                </tr>
                                                                            @endforeach
                                                                        @endif


                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>


                            {{ Form::close() }}
                        @endif
                    @endforeach




                </div>
            </div>

            <div data-render="app">
                <manage-role-form create-title="{{ __('Create') }} {{ __('page.lbl_role') }}">
                </manage-role-form>

            </div>

        </div>
    </div>



    <script>
        function reset_permission(role_id) {

            // var url = "/app/permission-role/reset/" + role_id;
            var url = "{{ route('backend.permission-role.reset', ['role_id' => ':role_id']) }}";
            url = url.replace(':role_id', role_id);

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    successSnackbar(response.message);
                    window.location.reload();
                },
                error: function(response) {
                    alert('error');
                }
            });
        }



        function delete_role(role_id) {
            var url = "{{ route('backend.role.destroy', ['role' => ':role_id']) }}";
            url = url.replace(':role_id', role_id);

            $.ajax({
                url: url,
                type: 'DELETE',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#permission_' + role_id).hide();
                    successSnackbar(response.message);

                },
                error: function(response) {
                    alert('error');
                }
            });
        }
    </script>



    @push('after-scripts')
        <script src="{{ mix('js/vue.min.js') }}"></script>
        <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
    @endpush

    <style>
        .permission-collapse table tr td.hiddenRow {
            padding: 0;
        }

        .permission-collapse table tr td.hiddenRow table td {
            padding: 20px;
        }

        .permission-collapse table tr td.hiddenRow table tr:last-child td {
            border: none;
        }
    </style>


@endsection
