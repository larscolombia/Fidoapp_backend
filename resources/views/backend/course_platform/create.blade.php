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

                {{-- <div class="mb-3">
                    <label for="duration" class="form-label">{{ __('course_platform.duration') }}</label>
                    <input type="text" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration') }}" required>
                    @error('duration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}
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

               <div class="d-lg-flex justify-content-lg-between">
                <div class="mb-3 col-lg-6">
                    <label for="price" class="form-label ">{{ __('course_platform.price') }}</label>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!--currency-->

                <div class="mb-3 col-lg-5">
                    <label for="currency_id" class="form-label">{{ __('course_platform.currency') }}</label>
                    <select class="form-control" name="currency_id" id="currency_id" required>
                        <option value="">{{__('course_platform.select')}}</option>
                        @foreach ($currencies as $currency)
                        <option value="{{$currency->id}}" @selected($currency->id == old('currency_id'))>{{$currency->currency_name}} ({{$currency->currency_symbol}})</option>
                        @endforeach
                    </select>
                    @error('currency_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <!--endcurrency-->
               </div>
                    <div class="mb-3" id="video-container">
                        <fieldset>
                        <label for="video" class="form-label">{{ __('course_platform.video') }}</label>
                        <div class="video-item mb-2">
                            <input type="file" class="form-control @error('video.*') is-invalid @enderror" name="video[]" accept="video/*" required>
                            <input type="text" name="title[]" class="form-control mt-2" placeholder="{{ __('course_platform.video_title') }}" required>
                            <input type="url" name="url_youtube[]" class="form-control mt-2" placeholder="{{ __('course_platform.url_youtube') }}" >
                            <label class="mt-2">{{ __('course_platform.thumbnail') }}</label>
                            <input type="file" class="form-control @error('thumbnail.*') is-invalid @enderror" name="thumbnail[]" accept="image/*">
                            <div class="video-preview border p-3 d-flex mt-2" style="width: 320px; height: 180px;"></div>
                        </div>
                    </fieldset>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-secondary my-3" id="add-video-button">{{ __('course_platform.add_video') }}</button>
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
    // Función para manejar la vista previa del video
    function handleVideoPreview(input) {
        const files = input.files; // Obtener todos los archivos seleccionados
        const videoPreview = input.closest('.video-item').querySelector('.video-preview'); // Seleccionar el contenedor de vista previa correspondiente
        videoPreview.innerHTML = ''; // Limpiar las vistas previas anteriores

        if (files.length > 0) { // Verifica si hay archivos seleccionados
            const file = files[0]; // Solo tomamos el primer archivo

            if (file && file.type.startsWith('video/')) { // Verifica si es un archivo de video
                const videoElement = document.createElement('video');
                videoElement.src = URL.createObjectURL(file); // Crea un objeto URL para el archivo
                videoElement.controls = true; // Muestra los controles del video
                videoElement.width = 320; // Establecer el ancho del elemento de video
                videoElement.height = 180; // Establecer la altura del elemento de video

                // Agregar eventos para pausar después de 10 segundos
                videoElement.currentTime = 0;
                // videoElement.addEventListener('loadedmetadata', function() {
                //     if (videoElement.duration > 10) {
                //         videoElement.currentTime = 10; // Salta a los 10 segundos si dura más
                //     }
                // });
                // videoElement.addEventListener('timeupdate', function() {
                //     if (videoElement.currentTime >= 10) {
                //         videoElement.pause(); // Pausa el video al llegar a los 10 segundos
                //     }
                // });

                // Agregar el elemento de video al contenedor de previsualización
                videoPreview.appendChild(videoElement);
            } else {
                alert("Por favor, selecciona un archivo de video válido.");
            }
        }
    }

    document.getElementById('add-video-button').addEventListener('click', function() {
        const videoContainer = document.getElementById('video-container');

        // Crear un nuevo fieldset para los campos de video
        const newFieldset = document.createElement('fieldset');
        newFieldset.classList.add('mb-3');

        newFieldset.innerHTML = `
            <label for="video" class="form-label">{{ __('course_platform.video') }}</label>
            <div class="video-item mb-2">
                <input type="file" class="form-control @error('video.*') is-invalid @enderror" name="video[]" accept="video/*" required>
                 <input type="text" name="title[]" class="form-control mt-2" placeholder="{{ __('course_platform.video_title') }}" required>
                <input type="url" name="url_youtube[]" class="form-control mt-2" placeholder="{{ __('course_platform.url_youtube') }}" >
                 <label class="mt-2">{{ __('course_platform.thumbnail') }}</label>
                <input type="file" class="form-control @error('thumbnail.*') is-invalid @enderror" name="thumbnail[]" accept="image/*">
                <button type="button" class="btn btn-danger mt-2 remove-video-button">Eliminar Video</button>
                <div class="video-preview border p-3 d-flex mt-2" style="width: 320px; height: 180px;"></div>
            </div>
        `;

        // Añadir el nuevo fieldset al contenedor principal
        videoContainer.appendChild(newFieldset);

        // Agregar evento al botón de eliminar en el nuevo conjunto
        newFieldset.querySelector('.remove-video-button').addEventListener('click', function() {
            videoContainer.removeChild(newFieldset);
        });

        // Agregar evento para la vista previa del video
        const fileInput = newFieldset.querySelector('input[type=file]'); // Selecciona el input del video

        fileInput.addEventListener('change', function() {
            handleVideoPreview(fileInput); // Llama a la función de vista previa solo si hay un archivo seleccionado
        });
    });

    // Asignar evento al primer input existente para manejar su previsualización
    const initialFileInput = document.querySelector('#video-container .video-item input[type=file]');
    initialFileInput.addEventListener('change', function() {
        handleVideoPreview(initialFileInput); // Llama a la función de vista previa para el primer input
    });
</script>
@endpush
