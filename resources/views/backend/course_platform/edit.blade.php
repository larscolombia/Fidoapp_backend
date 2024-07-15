@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.course_platform.update', ['curso_plataforma' => $course_platform->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('course_platform.name') }}</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $course_platform->name }}" placeholder="{{ __('course_platform.enter_name') }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('course_platform.description') }}</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="{{ __('course_platform.enter_description') }}">{{ $course_platform->description }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="url" class="form-label">{{ __('course_platform.url') }}</label>
                    <input type="url" class="form-control" id="url" name="url" value="{{ $course_platform->url }}" placeholder="{{ __('course_platform.enter_url') }}" required>
                    <div id="video-preview" class="mt-3"></div>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">{{ __('course_platform.price') }}</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ $course_platform->price }}" placeholder="{{ __('course_platform.enter_price') }}" required>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('course_platform.update') }}</button>
            </form>
        </div>
    </div>
@endsection

@push ('after-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlInput = document.getElementById('url');
        const videoPreview = document.getElementById('video-preview');
        const initialUrl = urlInput.value;
        updateVideoPreview(initialUrl);

        urlInput.addEventListener('input', function() {
            const url = this.value;
            updateVideoPreview(url);
        });

        function updateVideoPreview(url) {
            videoPreview.innerHTML = '';

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
                videoPreview.innerHTML = 'Video inválido o no soportado. Por favor, ingrese un enlace de YouTube o Vimeo.';
            }
        }

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
    });
</script>
@endpush