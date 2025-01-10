<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ language_direction() }}" class="theme-fs-sm">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="{{ asset(setting('logo')) }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset(setting('favicon')) }}">
    <meta name="keyword" content="{{ setting('meta_keyword') }}">
    <meta name="description" content="{{ setting('meta_description') }}">
    <meta name="setting_options" content="{{ setting('customization_json') }}">
    <!-- Shortcut Icon -->
    <link rel="shortcut icon" href="{{ asset(setting('favicon')) }}">
    <link rel="icon" type="image/ico" href="{{ asset(setting('favicon')) }}" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app_name" content="{{ app_name() }}">

    <title> {{ app_name() }}</title>

    <link rel="stylesheet" href="{{ mix('css/icon.min.css') }}">

    @stack('before-styles')
    <link rel="stylesheet" href="{{ mix('css/libs.min.css') }}">
    <link rel="stylesheet" href="{{ mix('css/backend.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark.css') }}">
    <link rel="stylesheet" href="{{ asset('custom-css/dashboard.css') }}">

    @if (language_direction() == 'rtl')
        <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
    @endif

    <link rel="stylesheet" href="{{ asset('css/customizer.css') }}">

    <style>
        :root {
            <?php
            $rootColors = setting('root_colors'); // Assuming the setting() function retrieves the JSON string

            // Check if the JSON string is not empty and can be decoded
            if (!empty($rootColors) && is_string($rootColors)) {
                $colors = json_decode($rootColors, true);

                // Check if decoding was successful and the colors array is not empty
                if (json_last_error() === JSON_ERROR_NONE && is_array($colors) && count($colors) > 0) {
                    foreach ($colors as $key => $value) {
                        echo $key . ': ' . $value . '; ';
                    }
                } else {
                    echo 'Invalid JSON or empty colors array.';
                }
            }
            ?>
        }
    </style>


    <link rel="stylesheet" href="{{ asset('icomoon/style.css') }}">
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Poetsen+One&display=swap"
        rel="stylesheet">
    @stack('after-styles')

    <x-google-analytics />
</head>

<body>
    <!-- Loader Start -->
    <div id="loading">
        <x-partials._body_loader />
    </div>
    <style>
        body {
            font-family: "Lato", serif;
            font-style: normal;
            background-color: #FEF7E5;
            color: #000;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: "Poetsen One", serif;
            font-style: normal;
            color: #FF4931;
        }

        .descriptions {
            font-weight: bold;
        }

        .circle {
            position: absolute;
            top: 0;
            right: 0;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: {{ $color }};
            z-index: 10;
        }

        .col-12.col-lg-6 {
            position: relative;
        }

        .container-pet {
            margin-top: -40px;
            z-index: 100000000;
            background-color: #fff;
            border-top-left-radius: 30px;
            border-top-right-radius: 30px;
        }

        .chip {

            color: #000;
        }

        .color-chip {
            color: #FC9214;
        }

        .backroung-chip {
            background-color: #FEF7E5;
        }

        .icon-pet {
            width: 24px;
            height: 24px;
        }

        .pet-image{
            max-height: 300px;
        }

        .services {
            border-top-left-radius: 30px;
            border-top-right-radius: 30px;
            border-bottom-right-radius: 30px;
            border-bottom-left-radius: 30px;
        }
    </style>
    <div class="container mt-5 mb-5">
        <section class="row justify-content-center align-items-center align-content-center">
            <div class="col-12 col-lg-6 border bg-white rounded p-0">

                <div class="form-group">
                    <div class="text-center">
                        <img src="{{ $pet->pet_image }}" alt="pet-image" class="img-fluid w-100 pet-image" />
                    </div>
                </div>
                <div class="circle"></div> <!-- Aquí está el círculo -->

                <div class="border container-pet position-relative p-3 border">
                    <h2 class="text-center">Perfil de la Mascota</h2>
                    <div class="form-group border rounded-pill p-1 mt-5 backroung-chip">
                        <div class="d-flex ms-5">
                            <div class="d-flex align-items-center ms-5">
                                <img class="icon-pet" src="{{ asset('img/pet/vector.png') }}" alt="vector.png">
                                <div>
                                    <label class="form-label chip ms-5 mt-2 descriptions"
                                        for="chip">{{ __('pet.pet_chip') }}</label>
                                    @if (isset($pet->chip))
                                        <p class="chip color-chip ms-5 descriptions">
                                            {{ $pet->chip->num_identificacion }}</p>
                                    @else
                                        <p class="chip color-chip ms-5 descriptions">{{ __('pet.unassigned') }}</p>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                    <h4 class="text-center mb-3">Información de la mascota</h4>
                    <div class="form-group p-3 border-bottom">
                        <label class="form-label" for="name">{{ __('pet.lbl_name') }}</label>
                        <p class="descriptions">{{ $pet->name }}</p>

                    </div>

                    <div class="form-group p-3 border-bottom">
                        <label class="form-label" for="pettype_id">{{ __('pet.species') }} </label>
                        <p class="descriptions">
                            {{ !is_null($pet->pettype) ? $pet->pettype->name : __('pet.unspecified') }}</p>

                    </div>

                    <!-- Repite la estructura anterior para los demás campos -->

                    <div class="form-group p-3 border-bottom">
                        <label for="" class="form-label">{{ __('customer.lbl_gender') }}</label>
                        <p class="descriptions">
                            {{ !is_null($pet->gender) ? ($pet->gender == 'Male' ? 'Macho' : 'Hembra') : __('pet.unspecified') }}
                        </p>
                    </div>

                    <div class="form-group p-3 border-bottom">
                        <label for="breed" class="form-label">{{ __('pet.lbl_breed') }}</label>
                        <p class="descriptions">{{ !is_null($pet->breed) ? $pet->breed->name : __('pet.unspecified') }}
                        </p>
                    </div>

                    <div class="form-group p-3 border-bottom">
                        <label for="date_of_birth" class="form-label">{{ __('pet.lbl_date_of_birth') }}</label>
                        <p class="descriptions">
                            {{ !is_null($pet->date_of_birth) ? $pet->date_of_birth : __('pet.unspecified') }}</p>
                    </div>

                    <div class="form-group p-3 border-bottom">
                        <label for="pet_fur" class="form-label">{{ __('pet.coat_color') }}</label>
                        <p class="descriptions">{{ !is_null($pet->pet_fur) ? $pet->pet_fur : __('pet.unspecified') }}
                        </p>
                    </div>

                    <div class="form-group p-3 border-bottom">
                        <label for="weight" class="form-label">{{ __('pet.lbl_weight') }}</label>
                        <p class="descriptions">{{ !is_null($pet->weight) ? $pet->weight : __('pet.unspecified') }}
                            {{ !is_null($pet->weight) ? (!is_null($pet->weight_unit) ? $pet->weight_unit : 'KG') : '' }}
                        </p>
                    </div>

                    <div class="form-group p-3 border-bottom">
                        <label for="height" class="form-label">{{ __('pet.lbl_height') }}</label>
                        <p class="descriptions">{{ !is_null($pet->height) ? $pet->height : __('pet.unspecified') }}
                            {{ !is_null($pet->height) ? (!is_null($pet->height_unit) ? $pet->height_unit : 'CM') : '' }}
                        </p>
                    </div>

                    <div class="form-group p-3 ">
                        <label for="owner" class="form-label">{{ __('pet.lbl_owner') }}</label>
                        <p class="descriptions">
                            {{ !is_null($pet->user) ? $pet->user->full_name : __('pet.unspecified') }}
                        </p>
                    </div>

                    <div class="row p-1 ms-3">
                        <h4 class="text-center mb-3">Datos de vacunación y tratamiento</h4>
                        @if (count($pet->histories) > 0)
                            @foreach ($pet->histories->sortByDesc('created_at') as $index => $history)
                                {{-- <div class=" form-group p-3 border-bottom">
                                        <label for="date" class="form-label">{{ __('pet.date') }}</label>
                                        <p class="descriptions">
                                            {{ !is_null($history->application_date) ? \Carbon\Carbon::parse($history->application_date)->format('d-m-Y') : __('pet.unspecified') }}
                                        </p>
                                    </div>
                                    <div class=" form-group p-3 border-bottom">
                                        <label for="name_history" class="form-label">{{ __('pet.lbl_description') }}</label>
                                        <p class="descriptions">
                                            {{ !is_null($history->name) ? $history->name : __('pet.unspecified') }}
                                        </p>
                                    </div>
                                    <div class=" form-group p-3  mb-3">
                                        <label for="condition_history"
                                            class="form-label">{{ __('pet.medical_condition') }}</label>
                                        <p class="descriptions">
                                            {{ !is_null($history->medical_conditions) ? $history->medical_conditions : __('pet.unspecified') }}
                                        </p>
                                    </div> --}}

                                @if (!is_null($history->vacuna))
                                    <div
                                        class="border services mb-3 col-5 me-auto">
                                        <h6 class="text-center mb-3 mt-3">Vacuna</h6>
                                        <div class=" form-group p-2 ">
                                            <label for="condition_history"
                                                class="form-label">{{ __('pet.lbl_name') }}</label>
                                            <p class="descriptions">
                                                {{ !is_null($history->vacuna->vacuna_name) ? $history->vacuna->vacuna_name : __('pet.unspecified') }}
                                            </p>
                                        </div>
                                        <div class=" form-group p-2 ">
                                            <label for="date"
                                                class="form-label">{{ __('pet.application_date') }}</label>
                                            <p class="descriptions">
                                                {{ !is_null($history->vacuna->fecha_aplicacion) ? \Carbon\Carbon::parse($history->vacuna->fecha_aplicacion)->format('d-m-Y') : __('pet.unspecified') }}
                                            </p>
                                        </div>
                                        <div class=" form-group p-2 ">
                                            <label for="date"
                                                class="form-label">{{ __('pet.reinforcement_date') }}</label>
                                            <p class="descriptions">
                                                {{ !is_null($history->vacuna->fecha_refuerzo_vacuna) ? \Carbon\Carbon::parse($history->vacuna->fecha_refuerzo_vacuna)->format('d-m-Y') : __('pet.unspecified') }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                @if (!is_null($history->antigarrapata))
                                    <div
                                        class="border services mb-3 col-5 me-auto">
                                        <h6 class="text-center mb-3 mt-3">Antigarrapata</h6>
                                        <div class=" form-group p-2 ">
                                            <label for="condition_history"
                                                class="form-label">{{ __('pet.lbl_name') }}</label>
                                            <p class="descriptions">
                                                {{ !is_null($history->antigarrapata->antigarrapata_name) ? $history->antigarrapata->antigarrapata_name : __('pet.unspecified') }}
                                            </p>
                                        </div>
                                        <div class=" form-group p-2 ">
                                            <label for="date"
                                                class="form-label">{{ __('pet.application_date') }}</label>
                                            <p class="descriptions">
                                                {{ !is_null($history->antigarrapata->fecha_aplicacion) ? \Carbon\Carbon::parse($history->antigarrapata->fecha_aplicacion)->format('d-m-Y') : __('pet.unspecified') }}
                                            </p>
                                        </div>
                                        <div class=" form-group p-2 ">
                                            <label for="date"
                                                class="form-label">{{ __('pet.reinforcement_date') }}</label>
                                            <p class="descriptions">
                                                {{ !is_null($history->antigarrapata->fecha_refuerzo_antigarrapata) ? \Carbon\Carbon::parse($history->antigarrapata->fecha_refuerzo_antigarrapata)->format('d-m-Y') : __('pet.unspecified') }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                @if (!is_null($history->antiparasitante))
                                    <div
                                        class="border services mb-3 col-5 me-auto">
                                        <h6 class="text-center mb-3 mt-3">Antiparasitante</h6>
                                        <div class=" form-group p-2 ">
                                            <label for="condition_history"
                                                class="form-label">{{ __('pet.lbl_name') }}</label>
                                            <p class="descriptions">
                                                {{ !is_null($history->antiparasitante->antidesparasitante_name) ? $history->antiparasitante->antidesparasitante_name : __('pet.unspecified') }}
                                            </p>
                                        </div>
                                        <div class=" form-group p-2 ">
                                            <label for="date"
                                                class="form-label">{{ __('pet.application_date') }}</label>
                                            <p class="descriptions">
                                                {{ !is_null($history->antiparasitante->fecha_aplicacion) ? \Carbon\Carbon::parse($history->antiparasitante->fecha_aplicacion)->format('d-m-Y') : __('pet.unspecified') }}
                                            </p>
                                        </div>
                                        <div class=" form-group p-2 ">
                                            <label for="date"
                                                class="form-label">{{ __('pet.reinforcement_date') }}</label>
                                            <p class="descriptions">
                                                {{ !is_null($history->antiparasitante->fecha_refuerzo_antidesparasitante) ? \Carbon\Carbon::parse($history->antiparasitante->fecha_refuerzo_antidesparasitante)->format('d-m-Y') : __('pet.unspecified') }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <p class="descriptions">No posee ninguna vacuna ni tratamiento</p>
                        @endif
                    </div>
                    <div class="row p-1 ms-3">
                        <h4 class="text-center mb-3">Entrenamientos</h4>
                        @if (count($pet->diario) > 0)
                            @foreach ($pet->diario->sortByDesc('date') as $index => $diario)

                                <div
                                    class="border services mb-3 col-5 me-auto">
                                    <div class=" form-group p-2 ">
                                        <label for="date" class="form-label">{{ __('pet.date') }}</label>
                                        <p class="descriptions">
                                            {{ !is_null($diario->date) ? \Carbon\Carbon::parse($diario->date)->format('d-m-Y') : __('pet.unspecified') }}
                                        </p>
                                    </div>
                                    <div class=" form-group p-2 ">
                                        <label for="date" class="form-label">{{ __('pet.category') }}</label>
                                        <p class="descriptions">
                                            {{ !is_null($diario->category) ? $diario->category->name : __('pet.unspecified') }}
                                        </p>
                                    </div>
                                    <div class="col-12 form-group p-2 ">
                                        <label for="activity" class="form-label">{{ __('pet.activity') }}</label>
                                        <p class="descriptions">
                                            {{ !is_null($diario->actividad) ? $diario->actividad : __('pet.unspecified') }}
                                        </p>
                                    </div>
                                    <div class="col-12 form-group p-2">
                                        <label for="note" class="form-label">{{ __('pet.note') }}</label>
                                        <p class="descriptions">
                                            {{ !is_null($diario->notas) ? $diario->notas : __('pet.unspecified') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="descriptions">No posee ninguna entrenamiento</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>


    <script src="{{ mix('js/backend.js') }}"></script>
    {{-- <script src="{{ mix('js/iqonic-script/setting.min.js') }}"></script> --}}
    <script src="{{ asset('js/iqonic-script/utility.js') }}"></script>
    {{-- <script src="{{ asset('js/setting-init.js') }}"></script> --}}
    <script src="{{ asset('js/app.js') }}"></script>

    <script src="{{ mix('js/booking-form.min.js') }}"></script>


</body>

</html>
