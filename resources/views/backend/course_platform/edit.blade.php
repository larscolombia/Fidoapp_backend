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

                <div class="mb-3" id="video-container">
                    <!-- Loop through existing videos if any -->
                    @foreach($course_platform->videos as $video)
                        <fieldset class="video-item mb-2">
                            <label for="video[]" class="form-label">{{ __('course_platform.video') }}</label>
                            <input type="file"
                                   class="form-control @error("video.*") is-invalid @enderror"
                                   name="video[]" accept="video/*" onchange="handleVideoPreview(this)">
                            <!-- Include existing video title and duration -->
                            <input type="text"
                                   name="title[]"
                                   class="form-control mt-2"
                                   value="{{ old("title.{$loop->index}", $video->title) }}"
                                   placeholder="{{ __('course_platform.video_title') }}" required>
                            <input type="hidden"
                            name="course_platform_id[]"
                            class="form-control mt-2"
                            value="{{ $video->id}}">
                            <input type="text"
                                   name="duration_video[]"
                                   class="form-control mt-2"
                                   value="{{ old("duration_video.{$loop->index}", $video->duration) }}"
                                   placeholder="{{ __('course_platform.duration') }}" required>

                            <!-- Thumbnail upload -->
                            <label>{{ __('course_platform.thumbnail') }}</label>
                            <input type="file"
                                   class="form-control @error("thumbnail.*") is-invalid @enderror"
                                   name="thumbnail[]"
                                   accept="image/*">

                            <!-- Video preview -->
                            <div class="video-preview border p-3 d-flex mt-2" >
                                @if($video->video)
                                    <video width="320" height="180" controls>
                                        <source src="{{ $video->url }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <p>No video uploaded</p>
                                @endif
                            </div>

                            <!-- Button to remove video -->
                            <button type="button" class="btn btn-danger mt-4 remove-video-button">Eliminar Video</button>
                        </fieldset>
                    @endforeach
                </div>


                <!-- Button to add more videos -->
                <div class ="mb-3">
                    <button type ="button"
                            class ="btn btn-secondary my-3"
                            id ="add-video-button">{{ __('course_platform.add_video') }}</button>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('course_platform.update') }}</button>
            </form>
        </div>
    </div>
@endsection

@push('after-scripts')
<script>
// Función para manejar la vista previa del video
function handleVideoPreview(input) {
    const files = input.files;
    const videoPreview = input.closest('.video-item').querySelector('.video-preview');
    videoPreview.innerHTML = '';

    if (files.length > 0) {
        const file = files[0];

        if (file && file.type.startsWith('video/')) {
            const videoElement = document.createElement('video');
            videoElement.src = URL.createObjectURL(file);
            videoElement.controls = true;
            videoElement.width = 320;
            videoElement.height = 180;

            // Agregar eventos para pausar después de 10 segundos
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

            // Agregar el elemento de video al contenedor de previsualización
            videoPreview.appendChild(videoElement);
        } else {
            alert("Por favor, selecciona un archivo de video válido.");
        }
    }
}

// Función para manejar la vista previa del thumbnail
function handleThumbnailPreview(input) {
    const files = input.files;
    const thumbnailPreview = input.closest('.video-item').querySelector('.thumbnail-preview');
    thumbnailPreview.innerHTML = '';

    if (files.length > 0) {
        const file = files[0];

        if (file && file.type.startsWith('image/')) {
            const imgElement = document.createElement('img');
            imgElement.src = URL.createObjectURL(file);
            imgElement.style.maxWidth = '100%'; // Ajusta el tamaño según sea necesario
            imgElement.style.maxHeight = '100%'; // Ajusta el tamaño según sea necesario

            // Agregar el elemento de imagen al contenedor de previsualización
            thumbnailPreview.appendChild(imgElement);
        } else {
            alert("Por favor, selecciona un archivo de imagen válido.");
        }
    }
}

// Asignar evento al primer input existente para manejar su previsualización
document.querySelectorAll('#video-container .video-item input[type=file]').forEach(input => {
    input.addEventListener('change', function() {
        if (input.name.startsWith('thumbnail')) {
            handleThumbnailPreview(input); // Llama a la función para imágenes
        } else {
            handleVideoPreview(input); // Llama a la función para videos
        }
    });
});

// Modificar el evento del botón "Agregar Video" para incluir la previsualización del thumbnail
document.getElementById('add-video-button').addEventListener('click', function() {
    const videoContainer = document.getElementById('video-container');

    // Crear un nuevo fieldset para los campos de video
    const newFieldset = document.createElement('fieldset');
    newFieldset.classList.add('mb-3');

    newFieldset.innerHTML = `
        <label for="video" class="form-label">{{ __('course_platform.video') }}</label>
        <div class="video-item mb-2">
            <input type="file" class="form-control" name="new_video[]" accept="video/*" required onchange="handleVideoPreview(this)">
            <input type="text" name="title[]" class="form-control mt-2" placeholder="{{ __('course_platform.video_title') }}" required>
            <input type="text" class="form-control mt-2" name="duration_video[]" placeholder="{{ __('course_platform.duration') }}" required>

            <label class="mt-2">{{ __('course_platform.thumbnail') }}</label>
            <input type="file" class="form-control" name="new_thumbnail[]" accept="image/*" onchange="handleThumbnailPreview(this)">

            <input type="hidden" name="course_platform_id[]" class="form-control mt-2">

            <button type="button" class="btn btn-danger mt-2 remove-video-button">Eliminar Video</button>

            <!-- Contenedor separado para la vista previa del nuevo thumbnail -->
            <div class="thumbnail-preview border p-3 d-flex mt-2" style="width: 320px; height: 180px;"></div>

            <!-- Contenedor separado para la vista previa del nuevo video -->
            <div class="video-preview border p-3 d-flex mt-2" style="width: 320px; height: 180px;"></div>
        </div>
    `;

    // Añadir el nuevo fieldset al contenedor principal
    videoContainer.appendChild(newFieldset);

    // Agregar evento al botón de eliminar en el nuevo conjunto
    newFieldset.querySelector('.remove-video-button').addEventListener('click', function() {
        newFieldset.remove(); // Elimina el fieldset completo
    });

    // Agregar evento para la vista previa del nuevo video y thumbnail
    const fileInputs = newFieldset.querySelectorAll('input[type=file]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (input.name.startsWith('new_thumbnail')) {
                handleThumbnailPreview(input); // Llama a la función para imágenes
            } else {
                handleVideoPreview(input); // Llama a la función para videos
            }
        });
    });
});
</script>
@endpush
