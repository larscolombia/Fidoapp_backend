@extends('backend.layouts.app')

@section('title')
    {{ __('pet.lost_pets') }}
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/constant/style.css') }}">
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <x-backend.section-header>
                <div>
                    <x-backend.quick-action formId='quick-action-form-found-pet' url='{{ route("backend.lost_pets_store_ids") }}'>
                        <div class="">
                            <select name="action_type" class="form-control select2 col-12" id="quick-action-type"
                                style="width:100%">
                                <option value="">{{ __('messages.no_action') }}</option>
                                <option value="found_pet">{{ __('messages.found_pet') }}</option>
                            </select>
                        </div>
                    </x-backend.quick-action>
                </div>

                <x-slot name="toolbar">
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text" id="addon-wrapping"><i class="icon-Search"></i></span>
                        <input type="text" class="form-control form-control-sm dt-search"
                            placeholder="{{ __('activity_levels.search_placeholder') }}" aria-label="Search"
                            aria-describedby="addon-wrapping">

                    </div>
                </x-slot>
            </x-backend.section-header>
        </div>
        <div class="card-body p-0">
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>

        <div data-render="app">
            <customer-offcanvas default-image="{{ user_avatar() }}"
                create-title="{{ __('messages.create') }} {{ __('pet.lbl_owner') }}"
                edit-title="{{ __('messages.edit') }} {{ __('pet.lbl_owner') }}">
            </customer-offcanvas>
            <send-push-notification create-title="Send Push Notification"></send-push-notification>
            <change-password
                create-title="{{ __('messages.change_password') }}"></change-password>
            <assign-pet create-title="Assign
                Pet"></assign-pet>
                <pet-offcanvas create-title="{{ __('messages.create') }} {{ __('pet.title') }}"
                    edit-title="{{ __('messages.edit') }} {{ __('pet.title') }}">
                </pet-offcanvas>
                <pets-offcanvas create-title="{{ __('Create') }} {{ __('pet.title') }}"
                edit-title="{{ __('messages.edit') }} {{ __('pet.title') }}">
            </pets-offcanvas>
        </div>
    </div>

@endsection

@push('after-styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- DataTables Core and Extensions -->
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript" defer>
        const columns = [{
                name: 'check',
                data: 'check',
                title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTableFoundPet(this)">',
                width: '0%',
                exportable: false,
                orderable: false,
                searchable: false,
            },
            {
                data: 'name',
                name: 'name',
                title: "{{ __('pet.title') }}",
                orderable: true,
                searchable: true,
            },
            {
                data: 'breed_id',
                name: 'breed_id',
                title: "{{ __('pet.breed') }}",
                orderable: true,
                searchable: true,
            },
            {
                data: 'age',
                name: 'age',
                title: "{{ __('pet.age') }}",
                orderable: true,
                searchable: true,
            },
            {
                data: 'user_id',
                name: 'user_id',
                title: "{{ __('pet.lbl_owner') }}",
                orderable: true,
                searchable: true,
            },

            // {
            //   data: 'email',
            //   name: 'email',
            //   title: "{{ __('customer.lbl_Email') }}"
            // },

            {
                data: 'lost_date',
                name: 'lost_date',
                title: "{{ __('pet.report_date') }}"
            },

        ]

        const actionColumn = [{
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            width: '5%',
            title: "{{ __('customer.lbl_action') }}"
        }]


        let finalColumns = [
            ...columns,
            ...actionColumn
        ]

        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route('backend.lost_pet_data') }}',
                finalColumns,
            })

        })

        function resetQuickAction() {
            const actionValue = $('#quick-action-type').val();
            if (actionValue != '') {
                $('#quick-action-apply').removeAttr('disabled');

                if (actionValue == 'change-status') {
                    $('.quick-action-field').addClass('d-none');
                    $('#change-status-action').removeClass('d-none');
                } else {
                    $('.quick-action-field').addClass('d-none');
                }
            } else {
                $('#quick-action-apply').attr('disabled', true);
                $('.quick-action-field').addClass('d-none');
            }
        }

        $('#quick-action-type').change(function() {
            resetQuickAction()
        });

        $(document).on('update_quick_action', function() {
            // resetActionButtons()
        })
    </script>

<script>
    const dataTableRowCheckRating = (id) => {
        checkRow()
        if ($('.select-table-row:checked').length > 0) {
            $('#quick-action-form-found-pet').removeClass('form-disabled')
            //if at-least one row is selected
            document.getElementById('select-all-table').indeterminate = true
            $('#quick-actions').find('input, textarea, button, select').removeAttr('disabled')
        } else {
            //if no row is selected
            document.getElementById('select-all-table').indeterminate = false
            $('#select-all-table').attr('checked', false)
            resetActionButtons()
        }

        if ($('#datatable-row-' + id).is(':checked')) {
            $('#row-' + id).addClass('table-active')
        } else {
            $('#row-' + id).removeClass('table-active')
        }
    }


    const confirmSwal = async (message) => {
        return await Swal.fire({
            title: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#858482',
            confirmButtonText: "{{ __('rating.do_it') }}",
            cancelButtonText: "{{ __('rating.cancel') }}"
        }).then((result) => {
            return result
        })
    }

    window.confirmSwal = confirmSwal

    $('#quick-action-form-found-pet').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const url = form.attr('action');
        const message = $('[name="message_' + $('[name="action_type"]').val() + '"]').val();

        // Obtener IDs seleccionados
        const selectedCheckboxes = $('#datatable_wrapper .select-table-row:checked');

        // Crear un array para almacenar los datos

        const rowData = selectedCheckboxes.map(function() {

            return {
                id: $(this).val(), // ID del checkbox
                module: $('input[name="module_name[' + $(this).val() + ']"]')
                    .val() // Valor del input oculto
            };
        }).get();

        // Confirmar la acción
        confirmSwal(message).then((result) => {
            if (!result.isConfirmed) return;

            // Enviar datos a través de AJAX
            callActionAjaxRating({
                url: url,
                body: form.serialize() + '&rowData=' + JSON.stringify(
                    rowData) // Incluye rowData en el cuerpo de la solicitud
            });
        });
    });


    function callActionAjaxRating({
        url,
        body
    }) {
        $.ajax({
            type: 'POST',
            url: url,
            data: body,
            success: function(res) {
                if (res.status) {
                    window.successSnackbar(res.message)
                    window.renderedDataTable.ajax.reload(resetActionButtons, false)
                    const event = new CustomEvent('update_quick_action', {
                        detail: {
                            value: true
                        }
                    })
                    document.dispatchEvent(event)
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: res.message,
                        icon: 'error'
                    })
                    // window.errorSnackbar(res.message)
                }
            }
        })
    }


    const selectAllTableFoundPet = (source) => {
        const checkboxes = document.getElementsByName('datatable_ids[]');
        let atLeastOneChecked = false; // Variable para verificar si al menos un checkbox está seleccionado

        for (var i = 0, n = checkboxes.length; i < n; i++) {
            // Si el checkbox no está deshabilitado, se selecciona o deselecciona
            if (!$('input#' + checkboxes[i].id).prop('disabled')) {
                checkboxes[i].checked = source.checked;
            }

            // Verifica el estado del checkbox y actualiza la clase de la fila
            if ($('input#' + checkboxes[i].id).is(':checked')) {
                $('input#' + checkboxes[i].id).closest('tr').addClass('table-active');
                atLeastOneChecked = true; // Al menos un checkbox está seleccionado
            } else {
                $('input#' + checkboxes[i].id).closest('tr').removeClass('table-active');
            }
        }

        // Si al menos un checkbox está seleccionado, habilita los botones y quita la clase 'form-disabled'
        if (atLeastOneChecked) {
            $('#quick-action-form-found-pet').removeClass('form-disabled');
            $('#quick-actions').find('input, textarea, button, select').removeAttr('disabled');
            document.getElementById('select-all-table').indeterminate =
                false; // Asegúrate de que no esté en estado indeterminado
        } else {
            // Si no hay checkboxes seleccionados, vuelve a deshabilitar los botones
            resetActionButtons(); // Asegúrate de que esta función maneje la lógica de deshabilitar botones
            document.getElementById('select-all-table').indeterminate =
                false; // También puedes establecerlo en false aquí
            $('#quick-action-form-found-pet').addClass('form-disabled');
            $('#quick-actions').find('input, textarea, button, select').addAttr('disabled');
        }

        checkRow(); // Llama a tu función para verificar el estado de las filas
    }


    window.selectAllTable = selectAllTable

    $(document).on('submit', '.found_pet_store', function(e) {
        e.preventDefault(); // Evita el envío normal del formulario

        var form = $(this); // Obtiene el formulario
        var url = form.attr('action'); // URL del formulario
        var data = form.serialize(); // Serializa los datos del formulario

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(response) {
                // Maneja la respuesta aquí
                if (response.status) {
                    // Si la respuesta es exitosa, muestra un mensaje de éxito
                    Swal.fire({
                        title: 'Éxito',
                        text: response.message, // Mensaje recibido del servidor
                        icon: 'success',
                        confirmButtonText: "{{__('rating.accept')}}"
                    }).then(() => {
                        $('#datatable').DataTable().ajax.reload(); // Recarga el DataTable
                    });
                } else {
                    // Manejar errores o mensajes de error
                    Swal.fire({
                        title: 'Error',
                        text: response.message, // Mensaje de error recibido del servidor
                        icon: 'error',
                        confirmButtonText: "{{__('rating.accept')}}"
                    });
                }
            },
            error: function(xhr) {
                // Manejo de errores en caso de que falle la solicitud AJAX
                console.error(xhr);
                Swal.fire({
                    title: 'Error',
                    text: "Ocurrió un error al intentar aprobar el registro.",
                    icon: 'error',
                    confirmButtonText: "{{__('rating.accept')}}"
                });
            }
        });
    });
</script>
@endpush
