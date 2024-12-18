<form action="{{ $url ?? '' }}" id="{{$formId ?? 'quick-action-form'}}"
    class="form-disabled d-flex gap-3 align-items-stretch flex-md-row flex-column">
    @csrf
    {{ $slot }}
    <input type="hidden" name="message_change-is_featured" value="{{__('rating.message_change-status')}}">
    <input type="hidden" name="message_change-status" value="{{__('rating.message_change-status')}}">
    <input type="hidden" name="message_delete" value="{{__('rating.message_delete')}}">
    <input type="hidden" name="message_approve" value="{{__('rating.message_approve')}}">
    <input type="hidden" name="message_found_pet" value="{{__('messages.question_found_pet')}}">
    <button class="btn btn-soft-primary" id="quick-action-apply">Aplicar</button>
</form>
