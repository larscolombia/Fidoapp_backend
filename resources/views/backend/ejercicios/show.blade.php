@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('ejercicios.Name') }}</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $ejercicio->name }}" readonly>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">{{ __('ejercicios.Description') }}</label>
                <textarea class="form-control" id="description" name="description" rows="3" readonly>{{ $ejercicio->description }}</textarea>
            </div>

            <div class="mb-3">
                <label for="url" class="form-label">{{ __('ejercicios.URL') }}</label>
                <input type="url" class="form-control" id="url" name="url" value="{{ $ejercicio->url }}" readonly>
                <div id="video-preview" class="mt-3"></div>
            </div>

            <div class="mt-4">
                <a href="{{ route('backend.clases.ejercicios.edit', ['clase' => $ejercicio->clase_id, 'ejercicio' => $ejercicio->id]) }}" class="btn btn-primary">{{ __('ejercicios.Edit') }}</a>
                <a href="{{ route('backend.clases.ejercicios.index', ['clase' => $ejercicio->clase_id]) }}" class="btn btn-secondary">{{ __('ejercicios.Back') }}</a>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const url = "{{ $ejercicio->url }}";
        const videoPreview = document.getElementById('video-preview');

        if (isValidVideoUrl(url)) {
            const videoId = getVideoId(url);

            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                videoPreview.innerHTML = `<iframe width="560" height="315" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
            } else if (url.includes('vimeo.com')) {
                videoPreview.innerHTML = `<iframe src="https://player.vimeo.com/video/${videoId}" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>`;
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