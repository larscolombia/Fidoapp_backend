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

            {{-- <div class="mb-3">
                <label for="url" class="form-label">{{ __('courses.URL') }}</label>
                <input type="url" class="form-control" id="url" name="url" value="{{ $clase->url }}" readonly>
            </div> --}}

            <div class="mb-3">
                <label for="video-preview" class="form-label">{{ __('courses.Video Preview') }}</label>
                <div id="video-preview" class="border p-3" style="width: 100%; height: auto;">
                    @if($clase->video)
                        <video width="320" height="180" controls>
                            <source src="{{ asset($clase->video) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <p>{{ __('courses.no_video') }}</p>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">{{ __('courses.Price') }}</label>
                <input type="text" class="form-control" id="price" name="price" value="{{ $clase->price }}" readonly>
            </div>

            <div class="mb-3">
                <label for="course" class="form-label">{{ __('courses.Course') }}</label>
                <input type="text" class="form-control" id="course" name="course" value="{{ $clase->cursoPlataforma->name }}" readonly>
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
        const videoPreview = document.getElementById('video-preview');

        if (videoPreview && !videoPreview.querySelector('video')) {
            const videoElement = document.createElement('video');
            videoElement.src = "{{ asset($clase->video) }}";
            videoElement.controls = true;
            videoElement.width = 320;
            videoElement.height = 180;
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