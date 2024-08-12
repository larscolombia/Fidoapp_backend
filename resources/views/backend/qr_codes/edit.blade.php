@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.qr_code.update', $pet->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <!-- Otros campos del formulario -->
                
                <div class="mb-3">
                    <label for="qr_code" class="form-label">{{ __('pet.qr_code') }}</label>
                    <input type="text" class="form-control @error('qr_code') is-invalid @enderror" id="qr_code" name="qr_code" value="{{ old('qr_code', $pet->qr_code) }}">
                    @error('qr_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Contenedor para mostrar la imagen del QR -->
                @if($pet->qr_code)
                    <div class="mb-3">
                        <label for="qr_code_image" class="form-label">{{ __('pet.qr_code_image') }}</label>
                        <div>
                            <img src="{{ Storage::url('qr_codes/' . $pet->qr_code) }}" alt="{{ __('pet.qr_code_image') }}" class="img-fluid">
                        </div>
                    </div>
                @endif

                <!-- Otros campos del formulario -->

                <button type="submit" class="btn btn-primary">{{ __('pet.update') }}</button>
            </form>
        </div>
    </div>
@endsection