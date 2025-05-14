@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form
                action="{{ route('backend.course_platform.clases.update', ['course' => request()->route('course'), 'clase' => $clase->id]) }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="title" class="form-label">{{ __('clases.titles') }}</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                        name="title" value="{{ old('title', $clase->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- <div class="mb-3">
                    <label for="duration" class="form-label">{{ __('course_platform.duration') }}</label>
                    <input type="text" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration',$clase->duration) }}" required>
                    @error('duration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}
                {{-- <div class="mb-3">
                    <label for="url_youtube" class="form-label">{{ __('course_platform.url_youtube') }}</label>
                    <input type="url" class="form-control @error('url_youtube') is-invalid @enderror" id="url_youtube" name="url_youtube" value="{{ old('url_youtube',$clase->url_youtube) }}" required>
                    @error('url_youtube')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}
                {{-- <div class="mb-3">
                    <label for="video" class="form-label">{{ __('courses.Video') }}</label>
                    <input type="file" class="form-control @error('video') is-invalid @enderror" id="video" name="video" accept="video/*">
                    @error('video')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="video-preview" class="mt-3">
                        @if ($clase->video)
                            <video width="320" height="180" controls>
                                <source src="{{ asset($clase->video) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <p>{{ __('courses.no_video') }}</p>
                        @endif
                    </div>
                </div> --}}
                <div class="mb-3">
                    <label for="video" class="form-label">{{ __('clases.video') }} (youtube)</label>
                    <input type="url" class="form-control @error('video') is-invalid @enderror" id="video"
                        name="video" value="{{ old('video', $clase->video) }}" required>
                    @error('video')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="duration" class="form-label">{{ __('course_platform.duration') }} (Hora:Minutos)</label>
                    <input type="time" class="form-control @error('duration') is-invalid @enderror" id="duration"
                        name="duration" value="{{ old('duration', $clase->duration) }}" required>
                    @error('duration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="mt-2">{{ __('course_platform.thumbnail') }}</label>
                    <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" name="thumbnail"
                        accept="image/*" value="{{ old('thumbnail', $clase->thumbnail) }}">
                    @error('thumbnail')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="video-preview" class="form-label">{{ __('clases.video_preview') }}</label>
                    <div id="video-preview" class="border p-3" style="width: 100%; height: auto;"></div>
                </div>

                <button type="submit" class="btn btn-success" id="submit-button">{{ __('courses.Update') }}</button>
                <a href="{{ route('backend.course_platform.clases.index', ['course' => request()->route('course')]) }}"
                    class="btn btn-secondary">{{ __('Cancel') }}</a>
            </form>
        </div>
    </div>
@endsection

@push('after-scripts')
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const videoInput = document.getElementById('video');
            const videoPreview = document.getElementById('video-preview');

            videoInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                videoPreview.innerHTML = ''; // Clear the previous preview

                if (file) {
                    const videoElement = document.createElement('video');
                    videoElement.src = URL.createObjectURL(file);
                    videoElement.controls = true;
                    videoElement.width = 320; // Set the width of the video element
                    videoElement.height = 180; // Set the height of the video element
                    videoElement.currentTime = 0;
                    videoElement.addEventListener('loadedmetadata', function() {
                        if (videoElement.duration > 10) {
                            videoElement.currentTime = 10;
                        }
                    });
                    videoElement.addEventListener('timeupdate', function() {
                        if (videoElement.currentTime >= 10) {
                            videoElement.pause();
                        }
                    });
                    videoPreview.appendChild(videoElement);
                }
            });
        });
    </script> --}}
   <script>
  const inputVideo = document.getElementById('video');
  const previewContainer = document.getElementById('video-preview');

  function extractYoutubeId(url) {
    const regex = /(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
    const match = url.match(regex);
    return match ? match[1] : null;
  }

  function renderPreview(url) {
    const videoId = extractYoutubeId(url.trim());
    if (videoId) {
      previewContainer.innerHTML = `
        <iframe width="560" height="315"
          src="https://www.youtube.com/embed/${videoId}"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen>
        </iframe>`;
    } else {
      previewContainer.innerHTML = '';
    }
  }

  // Mostrar preview al cargar la página con el valor actual
  document.addEventListener('DOMContentLoaded', () => {
    renderPreview(inputVideo.value);
  });

  // Actualizar preview al cambiar el input
  inputVideo.addEventListener('input', () => {
    renderPreview(inputVideo.value);
  });
</script>
@endpush
