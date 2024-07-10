@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 1050;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <x-slot name="toolbar">
                    <a class="btn btn-primary" href="{{ route('backend.courses.index') }}">{{ __('Courses.Back') }}</a>
                </x-slot>
                {{ __('Courses.create') }}
            </x-backend.section-header>

            <form action="{{ route('backend.courses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="titulo" class="form-label">{{ __('Courses.Enter_title') }}</label>
                    <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo') }}" required>
                    @error('titulo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="enlace" class="form-label">{{ __('Courses.Enter_url') }}</label>
                    <input type="url" class="form-control @error('enlace') is-invalid @enderror" id="enlace" name="enlace" value="{{ old('enlace') }}" required>
                    @error('enlace')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="video-preview" class="form-label">{{ __('Courses.Video Preview') }}</label>
                    <div id="video-preview" class="border p-3" style="width: 100%; height: auto;"></div>
                </div>
                <button type="submit" class="btn btn-success" id="submit-button" disabled>{{ __('Courses.create') }}</button>
                <a href="{{ route('backend.courses.index') }}" class="btn btn-secondary">{{ __('Courses.Cancel') }}</a>
            </form>
        </div>
    </div>
@endsection

@push('after-styles')
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
<script src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
<script>
    document.getElementById('enlace').addEventListener('input', function() {
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
            videoPreview.innerHTML = 'Video invalido o no soportado. Por favor, ingrese un enlace de YouTube o Vimeo.';
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