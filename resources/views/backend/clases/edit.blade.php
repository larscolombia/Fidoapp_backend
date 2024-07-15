@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.course_platform.clases.update', ['course' => request()->route('course'), 'clase' => $clase->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('courses.Name') }}</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $clase->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('courses.Description') }}</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $clase->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="url" class="form-label">{{ __('courses.URL') }}</label>
                    <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $clase->url) }}" required>
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="video-preview" class="mt-3"></div>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">{{ __('courses.Price') }}</label>
                    <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $clase->price) }}" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success" id="submit-button">{{ __('courses.Update') }}</button>
                <a href="{{ route('backend.course_platform.clases.index', ['course' => request()->route('course')]) }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            </form>
        </div>
    </div>
@endsection

@push('after-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlInput = document.getElementById('url');
        const videoPreview = document.getElementById('video-preview');
        const submitButton = document.getElementById('submit-button');
        
        function updateVideoPreview() {
            const url = urlInput.value;
            videoPreview.innerHTML = ''; // Clear the previous preview

            if (isValidVideoUrl(url)) {
                submitButton.disabled = false;
                const videoId = getVideoId(url);
                if (url.includes('youtube.com') || url.includes('youtu.be')) {
                    videoPreview.innerHTML = `<iframe width="560" height="315" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
                } else if (url.includes('vimeo.com')) {
                    videoPreview.innerHTML = `<iframe src="https://player.vimeo.com/video/${videoId}" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>`;
                }
            } else {
                submitButton.disabled = true;
                videoPreview.innerHTML = 'Invalid video URL. Please enter a valid YouTube or Vimeo URL.';
            }
        }

        urlInput.addEventListener('input', updateVideoPreview);
        updateVideoPreview(); // Call the function to show the preview initially

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