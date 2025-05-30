@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <x-slot name="toolbar">
                    <a class="btn btn-primary"
                        href="{{ route('backend.course_platform.clases.index', ['course' => request()->route('course')]) }}">{{ __('clases.back') }}</a>
                </x-slot>
                {{ __('clases.create') }}
            </x-backend.section-header>

            <form action="{{ route('backend.course_platform.clases.store', ['course' => request()->route('course')]) }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label">{{ __('clases.titles') }}</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                        name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                {{-- <div class="mb-3">
                    <label for="duration" class="form-label">{{ __('course_platform.duration') }}</label>
                    <input type="text" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration') }}" required>
                    @error('duration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}
                {{-- <div class="mb-3">
                    <label for="url_youtube" class="form-label">{{ __('course_platform.url_youtube') }}</label>
                    <input type="url" class="form-control @error('url_youtube') is-invalid @enderror" id="url_youtube"
                        name="url_youtube" value="{{ old('url_youtube') }}" required>
                    @error('url_youtube')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}
                {{-- <div class="mb-3">
                    <label for="video" class="form-label">{{ __('clases.video') }}</label>
                    <input type="file" class="form-control @error('video') is-invalid @enderror" id="video" name="video" accept="video/*" required>
                    @error('video')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}
                <div class="mb-3">
                    <label for="video" class="form-label">{{ __('clases.video') }} (youtube)</label>
                    <input type="url" class="form-control @error('video') is-invalid @enderror" id="video"
                        name="video" required>
                    @error('video')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="duration" class="form-label">{{ __('course_platform.duration') }} </label>
                    <input type="text" class="form-control @error('duration') is-invalid @enderror" id="duration"
                        name="duration" value="{{ old('duration','00:00:00') }}" placeholder="HH:MM:SS"
                        pattern="^[0-9]+:[0-5][0-9]:[0-5][0-9]$" required>
                    @error('duration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="mt-2">{{ __('course_platform.thumbnail') }}</label>
                    <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" name="thumbnail"
                        accept="image/*" required>
                    @error('thumbnail')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                {{-- <div class="mb-3">
                    <label for="price" class="form-label">{{ __('clases.price') }}</label>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}

                <div class="mb-3">
                    <label for="video-preview" class="form-label">{{ __('clases.video_preview') }}</label>
                    <div id="video-preview" class="border p-3" style="width: 100%; height: auto;"></div>
                </div>

                <button type="submit" class="btn btn-success" id="submit-button">{{ __('clases.create') }}</button>
                <a href="{{ route('backend.course_platform.clases.index', ['course' => request()->route('course')]) }}"
                    class="btn btn-secondary">{{ __('clases.cancel') }}</a>
            </form>
        </div>
    </div>
@endsection

@push('after-styles')
    <link rel="stylesheet" href='{{ mix('modules/product/style.css') }}'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.6/jquery.inputmask.min.js"></script>
@endpush

@push('after-scripts')
    {{-- <script>
        document.getElementById('video').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const videoPreview = document.getElementById('video-preview');

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
    </script> --}}
    <script>
        const inputVideo = document.getElementById('video');
        const previewContainer = document.getElementById('video-preview');

        function extractYoutubeId(url) {
            // Expresión regular para extraer el ID del video
            const regex = /(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
            const match = url.match(regex);
            return match ? match[1] : null;
        }

        inputVideo.addEventListener('input', () => {
            const url = inputVideo.value.trim();
            const videoId = extractYoutubeId(url);

            if (videoId) {
                previewContainer.innerHTML = `
        <iframe width="560" height="315"
          src="https://www.youtube.com/embed/${videoId}"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen>
        </iframe>`;
            } else {
                previewContainer.innerHTML = ''; // Limpia la previsualización si no es válido
            }
        });
    </script>
@endpush
