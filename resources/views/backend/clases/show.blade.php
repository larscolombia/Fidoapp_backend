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
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('courses.name') }}</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $clase->name }}" readonly>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">{{ __('courses.description') }}</label>
                <textarea class="form-control" id="description" name="description" rows="3" readonly>{{ $clase->description }}</textarea>
            </div>

            <div class="mb-3">
                <label for="url" class="form-label">{{ __('courses.URL') }}</label>
                <input type="url" class="form-control" id="url" name="url" value="{{ $clase->url }}" readonly>
            </div>

            <div class="mb-3">
                <label for="video-preview" class="form-label">{{ __('courses.Video Preview') }}</label>
                <div id="video-preview" class="border p-3" style="width: 100%; height: auto;">
                    @if($clase->url)
                        @if(preg_match('/(youtube\.com|youtu\.be)/', $clase->url))
                            <iframe width="560" height="315" src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        @elseif(preg_match('/vimeo\.com/', $clase->url))
                            <iframe src="https://player.vimeo.com/video/{{ $videoId }}" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                        @else
                            <p>{{ __('Invalid video URL.') }}</p>
                        @endif
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">{{ __('courses.Price') }}</label>
                <input type="text" class="form-control" id="price" name="price" value="{{ $clase->price }}" readonly>
            </div>

            <div class="mb-3">
                <label for="course" class="form-label">{{ __('courses.Course') }}</label>
                <input type="text" class="form-control" id="course" name="course" value="{{ $clase->cursoPlataforma }}" readonly>
            </div>

            <div class="mt-4">
                <a href="{{ route('backend.course_platform.clases.edit', ['course' => request()->route('course'), 'clase' => $clase->id]) }}" class="btn btn-primary">{{ __('courses.edit') }}</a>
                <a href="{{ route('backend.course_platform.clases.index', ['course' => request()->route('course')]) }}" class="btn btn-secondary">{{ __('courses.Back') }}</a>
            </div>
        </div>
    </div>
@endsection

@push ('after-styles')
<link rel="stylesheet" href='{{ mix("modules/product/style.css") }}'>
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const url = "{{ $clase->url }}";
        const videoPreview = document.getElementById('video-preview');

        if (isValidVideoUrl(url)) {
            const videoId = getVideoId(url);
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                videoPreview.innerHTML = `<iframe width="560" height="315" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
            } else if (url.includes('vimeo.com')) {
                videoPreview.innerHTML = `<iframe src="https://player.vimeo.com/video/${videoId}" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>`;
            } else {
                videoPreview.innerHTML = 'Invalid video URL. Please enter a valid YouTube or Vimeo URL.';
            }
        }

        function isValidVideoUrl(url) {
            return /^(https?\:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$/.test(url) ||
                   /^(https?\:\/\/)?(www\.)?(vimeo\.com)\/.+$/.test(url);
        }

        function getVideoId(url) {
            const youtubeMatch = url.match(/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
            const vimeoMatch = url.match(/(?:https?:\/\/)?(?:www\.)?(?:vimeo\.com\/)([0-9]+)/);
            return youtubeMatch ? youtubeMatch[1] : vimeoMatch ? vimeoMatch[1] : null;
        }
    });
</script>
@endpush