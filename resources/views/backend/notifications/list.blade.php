<div class="card-header border-bottom p-3">
  <h5 class="mb-0">{{ __('messages.all_notifications') }} ({{ $all_unread_count }})</h5>
</div>
<div class="card-body overflow-auto card-header-border p-0 card-body-list max-17 scroll-thin">
    <div class="dropdown-menu-1 overflow-y-auto list-style-1 mb-0 notification-height">
        @if(isset($notifications) && count($notifications) > 0)

            @foreach($notifications->sortByDesc('created_at')->take(5) as $notification)
               @if($notification->data['data']['notification_group']=='booking')
                <div class="dropdown-item-1 float-none p-3 list-unstyled iq-sub-card  {{ $notification->read_at ? '':'notify-list-bg'}} ">
                  <a href="{{ route('backend.bookings.bookingShow', ['id' => $notification->data['data']['id']]) }}" class="">
                  <div class="d-flex justify-content-between">
                    <h6>{{ $notification->data['subject']}}</h6>
                      <h6>#{{ $notification->data['data']['id']}}</h6>
                    </div>
                    <div class="list-item d-flex">
                        <div class="me-3 mt-1">
                            <button type="button" class="btn btn-soft-primary btn-icon rounded-pill">
                                {{ strtoupper(substr($notification->data['data']['user_name'], 0, 1)) }}
                            </button>
                        </div>
                        <div class="list-style-detail">
                          @if($notification->data['data']['notification_type']=='new_booking')
                          @php
                              $bookingEmployeeTypeName = $notification->data['data']['booking_services_names'] == 'training' ? 'entrenamiento' : 'médico';
                          @endphp
                            <p class="text-body mb-1"> Reserva recibida para <span class="text-primary">{{ ($bookingEmployeeTypeName) }}</span> servicio por <span class="text-black">{{ ($notification->data['data']['user_name']) }}</span></p>
                            <div class="d-flex justify-content-between">
                                <p class="text-body">{{ ($notification->data['data']['booking_date']) }}</p>
                                <p class="text-body">{{ ($notification->data['data']['booking_time']) }}</p>
                            </div>
                            @elseif($notification->data['data']['notification_type']=='accept_booking')

                             <p class="text-body mb-1">Reserva <span class="text-primary">#{{ ($notification->data['data']['id']) }}</span> ha sido aceptada.</p>
                              <div class="d-flex justify-content-between">
                               <p class="text-body">{{ $notification->updated_at->format('d/m/Y') }}</p>
                                <p class="text-body">{{ $notification->updated_at->format('h:i A') }}</p>
                            </div>

                            @elseif($notification->data['data']['notification_type']=='reject_booking')

                             <p class="text-body mb-1">Reserva <span class="text-primary">#{{ ($notification->data['data']['id']) }}</span> ha sido rechazada.</p>
                              <div class="d-flex justify-content-between">
                               <p class="text-body">{{ $notification->updated_at->format('d/m/Y') }}</p>
                                <p class="text-body">{{ $notification->updated_at->format('h:i A') }}</p>
                            </div>

                             @elseif($notification->data['data']['notification_type']=='complete_booking')

                             <p class="text-body mb-1">Reserva <span class="text-primary">#{{ ($notification->data['data']['id']) }}</span> ha sido completada.</p>
                              <div class="d-flex justify-content-between">
                               <p class="text-body">{{ $notification->updated_at->format('d/m/Y') }}</p>
                                <p class="text-body">{{ $notification->updated_at->format('h:i A') }}</p>
                            </div>
                             @elseif($notification->data['data']['notification_type']=='cancel_booking')

                             <p class="text-body mb-1">Reserva <span class="text-primary">#{{ ($notification->data['data']['id']) }}</span> ha sido cancelada.</p>
                              <div class="d-flex justify-content-between">
                               <p class="text-body">{{ $notification->updated_at->format('d/m/Y') }}</p>
                                <p class="text-body">{{ $notification->updated_at->format('h:i A') }}</p>
                            </div>
                               @elseif($notification->data['data']['notification_type']=='accept_booking_request')

                             <p class="text-body mb-1">Solicitud de reserva <span class="text-primary">#{{ ($notification->data['data']['id']) }}</span> ha sido aceptada.</p>
                              <div class="d-flex justify-content-between">
                               <p class="text-body">{{ $notification->updated_at->format('d/m/Y') }}</p>
                                <p class="text-body">{{ $notification->updated_at->format('h:i A') }}</p>
                            </div>
                            @endif

                        </div>
                    </div>
                  </a>
                </div>
                @elseif ($notification->data['data']['notification_group']=='new_user')

                <div class="me-3 mt-1">
                    <div class="d-flex p-2">
                        <div class="mr-3">
                            <button type="button" class="btn btn-soft-primary btn-icon rounded-pill">
                                {{ strtoupper(substr($notification->data['data']['user_name'], 0, 1)) }}
                            </button>
                        </div>
                        <div class="list-style-detail p-3 pt-0">
                            <p class="text-body mb-1"> Nuevo registro de usuario</p>
                            <p><span class="text-black">{{ ($notification->data['data']['user_name']) }}</span></p>

                        </div>
                    </div>
                </div>

                @else
                   <div class="dropdown-item-1 float-none p-3 list-unstyled iq-sub-card  {{ $notification->read_at ? '':'notify-list-bg'}} ">
                     <a href="{{ route('backend.orders.show', ['id' => $notification->data['data']['id']]) }}" class="">
                     <div class="d-flex justify-content-between">
                    <h6>{{ $notification->data['subject']}}</h6>
                    <h6>{{ ($notification->data['data']['order_code']) }} </h6>
                    </div>
                    <div class="list-item d-flex">
                        <div class="me-3 mt-1">
                            <button type="button" class="btn btn-soft-primary btn-icon rounded-pill">
                                {{ strtoupper(substr($notification->data['data']['user_name'], 0, 1)) }}
                            </button>
                        </div>
                         <div class="list-style-detail">
                            @if($notification->data['data']['notification_type']=='order_placed')
                            <p class="text-body mb-1">Nueva orden recibida de <span class="text-black">{{ ($notification->data['data']['user_name']) }}.</span></p>
                            <div class="d-flex justify-content-between">
                                <p class="text-body">{{ ($notification->data['data']['order_date']) }}</p>
                                <p class="text-body">{{ ($notification->data['data']['order_time']) }}</p>
                            </div>

                             @elseif($notification->data['data']['notification_type']=='order_proccessing')
                            <p class="text-body mb-1">Orden <span class="text-black">{{ ($notification->data['data']['order_code']) }}</span> ha sido procesada.</p>
                            <div class="d-flex justify-content-between">
                                <p class="text-body">{{ ($notification->data['data']['order_date']) }}</p>
                                <p class="text-body">{{ ($notification->data['data']['order_time']) }}</p>
                            </div>

                             @elseif($notification->data['data']['notification_type']=='order_delivered')
                            <p class="text-body mb-1">Order <span class="text-black">{{ ($notification->data['data']['order_code']) }} </span> ha sido entregada.</p>
                            <div class="d-flex justify-content-between">
                                <p class="text-body">{{ ($notification->data['data']['order_date']) }}</p>
                                <p class="text-body">{{ ($notification->data['data']['order_time']) }}</p>
                            </div>

                              @elseif($notification->data['data']['notification_type']=='order_cancelled')
                            <p class="text-body mb-1">Order <span class="text-black">{{ ($notification->data['data']['order_code']) }} </span> ha sido cancelada.</p>
                            <div class="d-flex justify-content-between">
                                <p class="text-body">{{ ($notification->data['data']['order_date']) }}</p>
                                <p class="text-body">{{ ($notification->data['data']['order_time']) }}</p>
                            </div>

                            @else


                            @endif
                        </div>

                    </div>
                  </a>
                </div>
                @endif

            @endforeach
        @else
            <li class="list-unstyled dropdown-item-1 float-none p-3">
                <div class="list-item d-flex justify-content-center align-items-center">
                    <div class="list-style-detail ml-2 mr-2">
                    <h6 class="font-weight-bold">{{ __('messages.no_notification') }}</h6>
                    <p class="mb-0"></p>
                    </div>
                </div>
            </li>
        @endif
    </div>
</div>
<div class="card-footer py-2 border-top">
  <div class="d-flex align-items-center justify-content-between">
      @if($all_unread_count > 0 )
        <a href="#" data-type="markas_read" class="text-primary mb-0 notifyList pull-right" ><span>{{__('messages.mark_all_as_read') }}</span></a>
      @endif
      @if(isset($notifications) && count($notifications) > 0)
        <a href="{{ route('backend.notifications.index') }}" class="btn btn-sm btn-primary">{{ __('messages.view_all') }}</a>
      @endif
  </div>
</div>
{{-- @if(isset($notifications) && count($notifications) > 0)
<div class="card-footer text-muted p-3 text-center ">
    <a href="{{ route('backend.notifications.index') }}" class="mb-0 btn-link btn-link-hover font-weight-bold view-all-btn">{{ __('messages.view_all') }}</a>
</div>
@endif --}}
