@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <x-slot name="toolbar">
                    <a class="btn btn-primary" href="{{ route('backend.course_platform.clases.index', ['course' => request()->route('course')]) }}">{{ __('clases.back') }}</a>
                </x-slot>
                {{ __('clases.create') }}
            </x-backend.section-header>

            <form action="{{ route('backend.course_platform.clases.store', ['course' => request()->route('course')]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('clases.name') }}</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('clases.description') }}</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="url" class="form-label">{{ __('clases.url') }}</label>
                    <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url') }}" required>
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">{{ __('clases.price') }}</label>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="video-preview" class="form-label">{{ __('clases.video_preview') }}</label>
                    <div id="video-preview" class="border p-3" style="width: 100%; height: auto;"></div>
                </div>

                <button type="submit" class="btn btn-success" id="submit-button" disabled>{{ __('clases.create') }}</button>
                <a href="{{ route('backend.course_platform.clases.index', ['course' => request()->route('course')]) }}" class="btn btn-secondary">{{ __('clases.cancel') }}</a>
            </form>
        </div>
    </div>
@endsection

@push('after-styles')
    <link rel="stylesheet" href='{{ mix("modules/product/style.css") }}'>
@endpush

@push('after-scripts')
    <script>
        document.getElementById('url').addEventListener('input', function() {
            const url = this.value;
            const videoPreview = document.getElementById('video-preview');
            const submitButton = document.getElementById('submit-button');
            
            videoPreview.innerHTML = ''; // Clear the previous preview

            if (isValidVideoUrl(url)) {
                submitButton.disabled = false;

                if (url.includes('youtube.com') || url.includes('youtu.be')) {
                    const videoId = getYouTubeVideoId(url);
                    if (videoId) {
                        videoPreview.innerHTML = `<iframe width="560" height="315" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
                    }
                } else if (url.includes('vimeo.com')) {
                    const videoId = getVimeoVideoId(url);
                    if (videoId) {
                        videoPreview.innerHTML = `<iframe src="https://player.vimeo.com/video/${videoId}" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>`;
                    }
                }
            } else {
                submitButton.disabled = true;
                videoPreview.innerHTML = 'Video inválido o no soportado. Por favor, ingrese un enlace de YouTube o Vimeo.';
            }
        });

        function isValidVideoUrl(url) {
            return (url.includes('youtube.com') || url.includes('youtu.be') || url.includes('vimeo.com'));
        }

        function getYouTubeVideoId(url) {
            const regex = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
            const match = url.match(regex);
            return match ? match[1] : null;
        }

        function getVimeoVideoId(url) {
            const regex = /(?:https?:\/\/)?(?:www\.)?(?:vimeo\.com\/)([0-9]+)/;
            const match = url.match(regex);
            return match ? match[1] : null;
        }
    </script>
@endpush