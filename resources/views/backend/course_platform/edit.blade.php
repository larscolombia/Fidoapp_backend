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

                {{-- <div class="mb-3">
                    <label for="url" class="form-label">{{ __('course_platform.url') }}</label>
                    <input type="url" class="form-control" id="url" name="url" value="{{ $course_platform->url }}" placeholder="{{ __('course_platform.enter_url') }}" required>
                    <div id="video-preview" class="mt-3"></div>
                </div> --}}

                <div class="mb-3">
                    <label for="price" class="form-label">{{ __('course_platform.price') }}</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ $course_platform->price }}" placeholder="{{ __('course_platform.enter_price') }}" required>
                </div>

                <div class="mb-3">
                    <label for="video" class="form-label">{{ __('course_platform.video') }}</label>
                    <input type="file" class="form-control" id="video" name="video" accept="video/*">
                    <div id="video-preview" class="mt-3 border p-3" style="width: 320px; height: 180px;">
                        @if ($course_platform->file)
                            <video width="320" height="180" controls>
                                <source src="{{ asset($course_platform->file) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">{{ __('course_platform.price') }}</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ $course_platform->price }}" placeholder="{{ __('course_platform.enter_price') }}" required>
                </div>

                <div class="mb-3">
                    <label for="duration" class="form-label">{{ __('course_platform.duration') }}</label>
                    <input type="text" class="form-control" id="duration" name="duration" value="{{ $course_platform->duration }}" placeholder="{{ __('course_platform.enter_duration') }}" required>
                </div>

                <!--difficulty-->
                <div class="mb-3">
                    <label for="difficulty" class="form-label">{{ __('course_platform.difficulty') }}</label>
                    <select class="form-control" name="difficulty" id="difficulty" required>
                        <option value="">{{__('course_platform.select')}}</option>
                        <option value="1" @selected($course_platform->difficulty==1)>{{__('course_platform.beginner')}}</option>
                        <option value="2" @selected($course_platform->difficulty==2)>{{__('course_platform.intermediate')}}</option>
                        <option value="3" @selected($course_platform->difficulty==3)>{{__('course_platform.advanced')}}</option>
                    </select>
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <!--enddifficulty-->

                <div class="mb-3">
                    <label for="image" class="form-label">{{ __('course_platform.image') }}</label>
                    <input type="file" class="form-control" id="image" name="image">
                    @if ($course_platform->image)
                        <img src="{{ asset($course_platform->image) }}" alt="Imagen del Curso" class="img-thumbnail mt-2" style="width: 200px;">
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">{{ __('course_platform.update') }}</button>
            </form>
        </div>
    </div>
@endsection

@push('after-scripts')
<script>
    document.getElementById('video').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const videoPreview = document.getElementById('video-preview');
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
</script>
@endpush