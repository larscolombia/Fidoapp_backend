<div class="d-flex gap-2 align-items-center">
    @hasPermission('edit_owners')
    @if ($data->lost == 1)
    <form action="{{route('backend.lost_pet_store_id',$data->id)}}" method="POST" class="found_pet_store">
        @csrf
        <button type="submit" class="fs-4 text-primary border-0 bg-transparent"
        title="{{ __('messages.found_pet') }}"> <i class="fas fa-check"></i></button>
    </form>
    @endif

    @endhasPermission

</div>
