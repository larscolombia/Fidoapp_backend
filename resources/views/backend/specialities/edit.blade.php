@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.specialities.update',$speciality->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('specialities.description') }}</label>
                    <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ $speciality->description ?? old('description') }}" required>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success" id="submit-button">{{ __('specialities.save') }}</button>
                <a href="{{ route('backend.specialities.index') }}" class="btn btn-secondary">{{ __('specialities.cancel') }}</a>
            </form>
        </div>
    </div>
@endsection

@push('after-styles')
    <link rel="stylesheet" href='{{ mix("modules/product/style.css") }}'>
@endpush

