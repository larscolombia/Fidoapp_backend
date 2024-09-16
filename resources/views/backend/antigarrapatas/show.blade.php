@extends('backend.layouts.app')

@section('title') {{ __('antigarrapata.Show Details') }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <h5 class="card-title">{{ __('antigarrapata.Antigarrapata Details') }}</h5>
                <div class="row">
                    <div class="col-md-6">
                        <strong>{{ __('antigarrapata.Pet Type') }}:</strong>
                        <p>{{ $antigarrapata->pet_type }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>{{ __('antigarrapata.Antigarrapata Name') }}:</strong>
                        <p>{{ $antigarrapata->antigarrapata_name }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>{{ __('antigarrapata.Fecha de Aplicación') }}:</strong>
                        <p>{{ $antigarrapata->fecha_aplicacion }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>{{ __('antigarrapata.Fecha de Refuerzo') }}:</strong>
                        <p>{{ $antigarrapata->fecha_refuerzo_antigarrapata }}</p>
                    </div>
                </div>
            </div>

            @hasPermission('edit_antigarrapatas')
                <a href="{{ route('backend.mascotas.antigarrapatas.edit', ['pet' => $antigarrapata->pet->id, 'antigarrapata' => $antigarrapata->id]) }}"
                   class="btn btn-warning">{{ __('antigarrapata.Edit') }}</a>
            @endhasPermission

            @hasPermission('delete_antigarrapatas')
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    {{ __('antigarrapata.Delete') }}
                </button>
            @endhasPermission

            <a href="{{ route('backend.mascotas.antigarrapatas.index', ['pet' => $antigarrapata->pet->id]) }}" class="btn btn-secondary">
                {{ __('antigarrapata.Back to List') }}
            </a>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">{{ __('antigarrapata.Confirm Deletion') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('antigarrapata.Are you sure you want to delete this antigarrapata?') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('antigarrapata.Cancel') }}</button>
                    <form id="deleteForm" action="{{ route('backend.antigarrapatas.destroy', ['antigarrapata' => $antigarrapata->id]) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('antigarrapata.Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
