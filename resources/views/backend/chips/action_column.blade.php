@if ($pet->chip)
    <!-- Botón de Editar Chip -->
    <a href="{{ route('backend.chips.edit', $pet->chip->id) }}" class="btn btn-sm btn-warning">
        {{ __('Editar Chip') }}
    </a>

    <!-- Botón para abrir el modal de eliminación -->
    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteChipModal-{{ $pet->id }}">
        {{ __('Eliminar Chip') }}
    </button>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="deleteChipModal-{{ $pet->id }}" tabindex="-1" aria-labelledby="deleteChipModalLabel-{{ $pet->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteChipModalLabel-{{ $pet->id }}">{{ __('Confirmar Eliminación') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('¿Estás seguro de que quieres eliminar este chip? Esta acción no se puede deshacer.') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancelar') }}</button>
                    <form action="{{ route('backend.chips.destroy', $pet->chip->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('Eliminar') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@else
    <!-- Botón de Crear Chip -->
    <a href="{{ route('backend.chips.create', ['pet_id' => $pet->id]) }}" class="btn btn-sm btn-primary">
        {{ __('Crear Chip') }}
    </a>
@endif