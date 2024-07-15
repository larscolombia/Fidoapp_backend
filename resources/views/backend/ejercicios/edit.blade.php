@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('backend.clases.ejercicios.update', ['clase' => request()->route('clase'), 'ejercicio' => $ejercicio->id]) }}" method="POST">
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

                <div class="mb-3">
                    <label for="url" class="form-label">{{ __('ejercicios.URL') }}</label>
                    <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $ejercicio->url) }}" required>
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="video-preview" class="mt-3"></div>
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
        const url = document.getElementById('url').value;
        const videoPreview = document.getElementById('video-preview');
        const submitButton = document.getElementById('submit-button');
        
        if (isValidVideoUrl(url)) {
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

        document.getElementById('url').addEventListener('input', function() {
            const url = this.value;
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
        });

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