@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/constant/style.css') }}">
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <x-backend.section-header>
                <div>
                    <x-backend.quick-action url='{{ route("backend.$module_name.bulk_action") }}'>
                        <div class="">
                            <select name="action_type" class="form-control select2 col-12" id="quick-action-type"
                                style="width:100%">
                                <option value="">{{ __('messages.no_action') }}</option>
                                <option value="change-status">{{ __('messages.status') }}</option>
                                @hasPermission('delete_owners')
                                    <option value="delete">{{ __('messages.delete') }}</option>
                                @endhasPermission
                            </select>
                        </div>
                        <div class="select-status d-none quick-action-field" id="change-status-action">
                            <select name="status" class="form-control select2" id="status" style="width:100%">
                                <option value="1">{{ __('messages.active') }}</option>
                                <option value="0">{{ __('messages.inactive') }}</option>
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

                    @hasPermission('add_owners')
                        <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('Create') }} {{ __('pet.lbl_owner') }}"
                            class=" d-flex align-items-center gap-1">{{ __('messages.new') }}</x-buttons.offcanvas>
                    @endhasPermission
                </x-slot>
            </x-backend.section-header>
        </div>
        <div class="card-body p-0">
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>
    </div>

    <div data-render="app_pets">
        <customer-offcanvas default-image="{{ user_avatar() }}"
            create-title="{{ __('messages.create') }} {{ __('pet.lbl_owner') }}"
            edit-title="{{ __('messages.edit') }} {{ __('pet.lbl_owner') }}">
        </customer-offcanvas>
        <send-push-notification create-title="Send Push Notification"></send-push-notification>
        <change-password create-title="{{ __('messages.change_password') }}"></change-password>
        <assign-pet create-title="Assign
            Pet"></assign-pet>
        <pet-offcanvas create-title="{{ __('messages.create') }} {{ __('pet.title') }}"
            edit-title="{{ __('messages.edit') }} {{ __('pet.title') }}">
        </pet-offcanvas>
        <pets-offcanvas create-title="{{ __('Create') }} {{ __('pet.title') }}"
            edit-title="{{ __('messages.edit') }} {{ __('pet.title') }}">
        </pets-offcanvas>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="" method="POST" id="shareForm">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="shareModalLabel">{{ __('pet.share') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="userSelect" class="form-label">{{ __('pet.Select User') }}</label>
                            <select class="form-select" id="userSelect" name="user[]" multiple>
                                <option value="">{{ __('pet.Select User') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('pet.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('pet.share') }}</button>
                    </div>
                </div>
            </form>
        </div>
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
    <script src="{{ mix('modules/pet/script.js') }}" defer></script>
    <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
    <!-- DataTables Core and Extensions -->
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript" defer>
        const columns = [{
                name: 'check',
                data: 'check',
                title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                width: '0%',
                exportable: false,
                orderable: false,
                searchable: false,
            },
            {
                name: 'serial_number',
                title: 'No.',
                data: null,
                width: '5%',
                render: function(data, type, row, meta) {
                    return meta.row + 1;
                },
                orderable: false,
                searchable: false,
            },
            {
                data: 'user_id',
                name: 'user_id',
                title: "{{ __('pet.lbl_name') }}",
                orderable: true,
                searchable: true,
            },

            // {
            //   data: 'email',
            //   name: 'email',
            //   title: "{{ __('customer.lbl_Email') }}"
            // },
            {
                data: 'mobile',
                name: 'mobile',
                title: "{{ __('customer.lbl_contact_number') }}"
            },
            @hasPermission("view_owner's_pet") {
                data: 'pet_count',
                name: 'pet_count',
                title: "{{ __('pet.lbl_pet_count') }}",
                orderable: false,
                searchable: false,
            },
            @endhasPermission

            {
                data: 'updated_at',
                name: 'updated_at',
                title: "{{ __('booking.lbl_update_at') }}"
            },

            {
                data: 'gender',
                name: 'gender',
                title: "{{ __('customer.lbl_gender') }}"
            },


            {
                data: 'status',
                name: 'status',
                orderable: false,
                searchable: true,
                title: "{{ __('customer.lbl_status') }}"
            },
            {
                data: 'created_at',
                name: 'created_at',
                visible: false,
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
                url: '{{ route("backend.$module_name.index_data") }}',
                finalColumns,
                orderColumn: [
                    @hasPermission("view_owner's_pet")[5, 'desc']
                    @else[4, 'desc']
                    @endhasPermission



                ],
            })

            shareModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget; // Button that triggered the modal
                var ownerId = button.getAttribute('data-owner-id'); // Extract info from data-* attributes

                // Make an AJAX request to get the users excluding the owner
                fetch(`/api/users-and-owners/${ownerId}`)
                    .then(response => response.json())
                    .then(data => {
                        var usersArray = Object.values(data.users);
                        var owners = data.sharedOwners;

                        var userSelect = document.getElementById('userSelect');
                        userSelect.innerHTML = ''; // Clear existing options

                        usersArray.forEach(user => {
                            var option = document.createElement('option');
                            option.value = user.id;
                            option.textContent = user.full_name;
                            // Preselecciona la opción si el ID del usuario está en el array selectedUsers
                            if (owners.includes(user.id)) {
                                option.selected = true;
                            }
                            userSelect.appendChild(option);
                        });

                        // Update the form action
                        var shareForm = document.getElementById('shareForm');
                        shareForm.action = `/app/pet/${ownerId}/shared-owner`;

                        // Initialize Select2 with dropdownParent option
                        $('#userSelect').select2({
                            placeholder: "{{ __('pet.Select User') }}",
                            allowClear: true,
                            dropdownParent: $(
                                '#shareModal'
                            ) // Ensure the dropdown is rendered within the modal
                        });
                    });
            });
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
@endpush
