@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.clases.ejercicios.update', ['clase' => request()->route('clase'), 'ejercicio' => $ejercicio->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('ejercicios.Name') }}</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $ejercicio->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('ejercicios.Description') }}</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $ejercicio->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Comentado la sección de URL -->
                <!--
                <div class="mb-3">
                    <label for="url" class="form-label">{{ __('ejercicios.URL') }}</label>
                    <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $ejercicio->url) }}" required>
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="video-preview" class="mt-3"></div>
                </div>
                -->

                <div class="mb-3">
                    <label for="video" class="form-label">{{ __('ejercicios.Video') }}</label>
                    <input type="file" class="form-control @error('video') is-invalid @enderror" id="video" name="video" accept="video/*">
                    @error('video')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="video-preview" class="mt-3">
                        @if($ejercicio->video)
                            <video width="560" height="315" controls>
                                <source src="{{ asset($ejercicio->video) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <p>{{ __('No video available') }}</p>
                        @endif
                    </div>
                </div>

                <button type="submit" class="btn btn-success" id="submit-button">{{ __('ejercicios.Update') }}</button>
                <a href="{{ route('backend.clases.ejercicios.index', ['clase' => request()->route('clase')]) }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
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
                videoElement.width = 560; // Set the width of the video element
                videoElement.height = 315; // Set the height of the video element
                videoPreview.appendChild(videoElement);
            }
        });
    });
</script>
@endpush