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

                {{-- <div class="mb-3">
                    <label for="url" class="form-label">{{ __('course_platform.url') }}</label>
                    <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url') }}" required>
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}

                <div class="mb-3">
                    <label for="video" class="form-label">{{ __('course_platform.video') }}</label>
                    <input type="file" class="form-control @error('video') is-invalid @enderror" id="video" name="video" accept="video/*" required>
                    @error('video')
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
                <!--difficulty-->
                <div class="mb-3">
                    <label for="difficulty" class="form-label">{{ __('course_platform.difficulty') }}</label>
                    <select class="form-control" name="difficulty" id="difficulty" required>
                        <option value="">{{__('course_platform.select')}}</option>
                        <option value="1">{{__('course_platform.beginner')}}</option>
                        <option value="2">{{__('course_platform.intermediate')}}</option>
                        <option value="3">{{__('course_platform.advanced')}}</option>
                    </select>
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <!--enddifficulty-->
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
                    <div id="video-preview" class="border p-3" style="width: 320px; height: 180px;"></div>
                </div>

                <button type="submit" class="btn btn-primary my-3" id="submit-button" >{{ __('course_platform.create') }}</button>
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