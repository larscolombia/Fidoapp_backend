@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection


@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/constant/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
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
                                <option value="delete">{{ __('messages.delete') }}</option>
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

                    <div>
                        <div class="datatable-filter">
                            <select name="column_status" id="column_status" class="select2 form-control p-10"
                                data-filter="select" style="width: 100%">
                                <option value="">{{ __('employee.all_commission') }}</option>
                                @foreach ($commissions_list as $key => $value)
                                    <option value="{{ $value->id }}"
                                        {{ $filter['commission'] == $value->title ? 'selected' : '' }}>{{ $value->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="input-group flex-nowrap">
                        <span class="input-group-text" id="addon-wrapping"><i class="icon-Search"></i></span>
                        <input type="text" class="form-control form-control-sm dt-search"
                            placeholder="{{ __('activity_levels.search_placeholder') }}" aria-label="Search"
                            aria-describedby="addon-wrapping">

                    </div>
                    @hasPermission('add_employees')
                        @if ($type != 'pending_employee')
                            <x-buttons.offcanvas target='#form-offcanvas'
                                title="{{ __('employee.create') }} {{ __($create_title) }}"
                                class=" d-flex align-items-center gap-1">{{ __('messages.new') }}</x-buttons.offcanvas>
                        @endif
                    @endhasPermission
                </x-slot>
            </x-backend.section-header>
        </div>
        <div class="card-body p-0">
            <table id="datatable" class="table table-striped border table-responsive">
            </table>
        </div>
    </div>

    <div data-render="app_employee">

        @if ($type != '')
            <employee-offcanvas type="{{ __($type) }}" default-image="https://dummyimage.com/600x300/cfcfcf/000000.png"
                create-title="{{ __('messages.create') }} {{ __($create_title) }}"
                edit-title="{{ __('messages.edit') }} {{ __($create_title) }}"
                :customefield="{{ json_encode($customefield) }}">
            </employee-offcanvas>
        @else
            <employee-offcanvas type="{{ __('staff') }}" default-image="https://dummyimage.com/600x300/cfcfcf/000000.png"
                create-title="{{ __('messages.create') }} {{ __($create_title) }}"
                edit-title="{{ __('messages.edit') }} {{ __($create_title) }}"
                :customefield="{{ json_encode($customefield) }}">
            </employee-offcanvas>
        @endif


        <employee-slot-mapping-form-offcanvas></employee-slot-mapping-form-offcanvas>
        <change-password create-title="{{ __('messages.change_password') }}"></change-password>

        <send-push-notification create-title="Send
            Push Notification"></send-push-notification>
    </div>
@endsection

@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script src="{{ mix('modules/employee/script.js') }}"></script>
    <script src="{{ asset('js/form-offcanvas/index.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
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
                data: 'user_id',
                name: 'user_id',
                title: "{{ __('employee.lbl_name') }}",
                orderable: true,
                searchable: true,
            },

            {
                data: 'mobile',
                name: 'mobile',
                title: "{{ __('employee.lbl_contact_number') }}"
            },
            @if (request('employee_type') == null)
                {
                    data: 'user_type',
                    name: 'user_type',
                    title: "{{ __('employee.lbl_role') }}"
                },
            @endif

            {
                data: 'email_verified_at',
                name: 'email_verified_at',
                orderable: true,
                searchable: false,
                title: "{{ __('employee.lbl_verification_status') }}"
            },
            {
                data: 'is_banned',
                name: 'is_banned',
                orderable: true,
                searchable: true,
                title: "{{ __('employee.lbl_blocked') }}"
            },
            {
                data: 'status',
                name: 'status',
                orderable: true,
                searchable: true,
                title: "{{ __('employee.lbl_status') }}"
            },
            {
                data: 'updated_at',
                name: 'updated_at',
                width: '15%',
                visible: false
            },
            {
                data: 'created_at',
                name: 'created_at',
                width: '15%',
                visible: false
            },
        ]

        const actionColumn = [{
            data: 'action',
            name: 'action',
            width: '5%',
            orderable: false,
            searchable: false,
            title: "{{ __('employee.lbl_action') }}"
        }]

        const customFieldColumns = JSON.parse(@json($columns))

        let finalColumns = [
            ...columns,
            ...customFieldColumns,
            ...actionColumn

        ]
        document.addEventListener('DOMContentLoaded', (event) => {
            initDatatable({
                url: '{{ route("backend.$module_name.index_data", ['type' => $type]) }}',
                finalColumns,

                orderColumn: [
                    @if (request('employee_type') == null)
                        [8, 'desc'],
                    @else
                        [7, 'desc'],
                    @endif

                ],

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
@endpush
