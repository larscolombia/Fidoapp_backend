@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form id="course-form" action="{{ route('backend.course_platform.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('course_platform.name') }}</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('course_platform.description') }}</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="url" class="form-label">{{ __('course_platform.url') }}</label>
                    <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url') }}" required>
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="duration" class="form-label">{{ __('course_platform.duration') }}</label>
                    <input type="text" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration') }}" required>
                    @error('duration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">{{ __('course_platform.image') }}</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" required>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">{{ __('course_platform.price') }}</label>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="video-preview" class="form-label">{{ __('course_platform.video_preview') }}</label>
                    <div id="video-preview" class="border p-3" style="width: 100%; height: auto;"></div>
                </div>

                <button type="submit" class="btn btn-primary" id="submit-button" disabled>{{ __('course_platform.create') }}</button>
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
    document.getElementById('url').addEventListener('input', function() {
        const url = this.value;
        const videoPreview = document.getElementById('video-preview');
        const submitButton = document.getElementById('submit-button');
        
        videoPreview.innerHTML = ''; // Clear the previous preview

        if (isValidVideoUrl(url)) {
            showVideoPreview(url, videoPreview);
            submitButton.disabled = false;
        } else {
            submitButton.disabled = true;
            videoPreview.innerHTML = 'Video invalido o no soportado. Por favor, ingrese un enlace de YouTube o Vimeo válido.';
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

    function showVideoPreview(url, videoPreview) {
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
    }
</script>
@endpush