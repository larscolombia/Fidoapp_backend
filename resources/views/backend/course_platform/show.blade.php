@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('course_platform.name') }}</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $course_platform->name }}" placeholder="{{ __('course_platform.enter_name') }}" readonly>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">{{ __('course_platform.description') }}</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="{{ __('course_platform.enter_description') }}" readonly>{{ $course_platform->description }}</textarea>
            </div>

            {{-- <div class="mb-3">
                <label for="url" class="form-label">{{ __('course_platform.url') }}</label>
                <input type="url" class="form-control" id="url" name="url" value="{{ $course_platform->url }}" placeholder="{{ __('course_platform.enter_url') }}" readonly>
                <div id="video-preview" class="mt-3"></div>
            </div> --}}

            <div class="mb-3">
                <label for="video" class="form-label">{{ __('course_platform.video') }}</label>
                <div id="video-preview" class="mt-3">
                    @if($course_platform->file)
                        <video width="320" height="180" controls>
                            <source src="{{ asset($course_platform->file) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <p>{{ __('course_platform.no_video') }}</p>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">{{ __('course_platform.price') }}</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ $course_platform->price }}" placeholder="{{ __('course_platform.enter_price') }}" readonly>
            </div>

            <div class="mb-3">
                <label for="duration" class="form-label">{{ __('course_platform.duration') }}</label>
                <input type="text" class="form-control" id="duration" name="duration" value="{{ $course_platform->duration }}" placeholder="{{ __('course_platform.enter_duration') }}" readonly>
            </div>

            <!--difficulty-->
            <div class="mb-3">
                <label for="difficulty" class="form-label">{{ __('course_platform.difficulty') }}</label>
                @php
                    $difficulties = [
                    1 => __('course_platform.beginner'),
                    2 => __('course_platform.intermediate'),
                    3 => __('course_platform.advanced'),
                    ];
                    $difficulty = $difficulties[$course_platform->difficulty] ?? '';
                @endphp
                <input type="text" class="form-control" name="difficulty" id="difficulty" value="{{ $difficulty }}" placeholder="{{ __('course_platform.difficulty') }}" readonly>
            </div>
            <!--enddifficulty-->

            <div class="mb-3">
                <label for="image" class="form-label">{{ __('course_platform.image') }}</label>
                @if($course_platform->image)
                    <img src="{{ asset($course_platform->image) }}" class="img-fluid" alt="{{ $course_platform->name }}" style="width: 200px;">
                @else
                    <p>{{ __('course_platform.no_image') }}</p>
                @endif
            </div>

            <div class="mt-4">
                <a href="{{ route('backend.course_platform.edit', ['curso_plataforma' => $course_platform->id]) }}" class="btn btn-primary">{{ __('course_platform.edit') }}</a>
                <a href="{{ route('backend.course_platform.index') }}" class="btn btn-secondary">{{ __('course_platform.back') }}</a>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        const url = '{{ $course_platform->url }}';
        const videoPreview = document.getElementById('video-preview');
        updateVideoPreview(url);

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
</script> --}}
@endpush