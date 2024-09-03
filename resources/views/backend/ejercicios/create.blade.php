@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.clases.ejercicios.store', ['clase' => $clase->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('ejercicios.Name') }}</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('ejercicios.Description') }}</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="video" class="form-label">{{ __('ejercicios.Video') }}</label>
                    <input type="file" class="form-control @error('video') is-invalid @enderror" id="video" name="video" accept="video/*" required>
                    @error('video')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="video-preview" class="mt-3"></div>
                </div>

                <button type="submit" class="btn btn-success" id="submit-button">{{ __('ejercicios.Create') }}</button>
                <a href="{{ route('backend.clases.ejercicios.index', ['clase' => $clase->id]) }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            </form>
        </div>
    </div>
@endsection

@push('after-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const videoInput = document.getElementById('video');
        const videoPreview = document.getElementById('video-preview');

        videoInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            videoPreview.innerHTML = ''; // Clear the previous preview

            if (file) {
                const videoElement = document.createElement('video');
                videoElement.src = URL.createObjectURL(file);
                videoElement.controls = true;
                videoElement.width = 320; // Set the width of the video element
                videoElement.height = 180; // Set the height of the video element
                videoElement.currentTime = 0;
                videoElement.addEventListener('loadedmetadata', function() {
                    if (videoElement.duration > 10) {
                        videoElement.currentTime = 10;
                    }
                });
                videoElement.addEventListener('timeupdate', function() {
                    if (videoElement.currentTime >= 10) {
                        videoElement.pause();
                    }
                });
                videoPreview.appendChild(videoElement);
            }
        });
    });
</script>
@endpush