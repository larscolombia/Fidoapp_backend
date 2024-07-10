@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <x-slot name="toolbar">
                    <a class="btn btn-primary" href="{{ route('backend.courses.index') }}">{{ __('Courses.Back') }}</a>
                </x-slot>
                {{ __('Courses.show') }}
            </x-backend.section-header>

            <div class="mb-3">
                <label for="titulo" class="form-label">{{ __('Courses.Enter_title') }}</label>
                <input type="text" class="form-control" id="titulo" name="titulo" value="{{ $course->titulo }}" readonly>
            </div>
            <div class="mb-3">
                <label for="enlace" class="form-label">{{ __('Courses.Enter_url') }}</label>
                <input type="url" class="form-control" id="enlace" name="enlace" value="{{ $course->enlace }}" readonly>
            </div>
            <div class="mb-3">
                <label for="video-preview" class="form-label">{{ __('Courses.Video Preview') }}</label>
                <div id="video-preview" class="border p-3" style="width: 100%; height: auto;"></div>
            </div>
            <div class="mt-4">
                <a href="{{ route('backend.courses.edit', $course->id) }}" class="btn btn-primary">{{ __('Courses.edit') }}</a>
                <a href="{{ route('backend.courses.index') }}" class="btn btn-secondary">{{ __('Courses.back') }}</a>
            </div>
        </div>
    </div>
@endsection

@push('after-styles')
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
<script src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const url = document.getElementById('enlace').value;
        const videoPreview = document.getElementById('video-preview');

        if (isValidVideoUrl(url)) {
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
            videoPreview.innerHTML = 'Invalid URL or unsupported video platform';
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