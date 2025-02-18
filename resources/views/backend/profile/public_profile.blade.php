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

        .text-18 {
            font-size: 18px;
            color: #FF4931;
        }

        .border-orange {
            border-color: #FC9214;
        }

        .descriptions {
            font-weight: bold;
        }

        .col-12.col-lg-6 {
            position: relative;
        }

        .container-user {
            margin-top: 75px;
            /* Ajusta para que la imagen quede encima */
            background-color: #fff;
            border-top-left-radius: 30px;
            border-top-right-radius: 30px;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            position: absolute;
            /* Coloca la imagen por encima del contenedor */
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            /* Asegura que esté sobre el contenedor */
            border: 4px solid #FC9214;
            /* Añade un borde blanco para destacar */
        }

        .container-user {

            z-index: 1;
            /* Asegura que el contenedor esté detrás de la imagen pero visible */
        }

        .services {
            border-radius: 30px;
            /* Simplificado con una sola propiedad */
        }
    </style>
    <div class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="container mt-5 mb-5 ">
            <section class="row justify-content-center align-items-center align-content-center">
                <div class="col-12 col-lg-6 rounded p-0 position-relative align-items-center align-content-center">
                    <!-- Imagen del perfil -->
                    <div class="form-group text-center position-relative">
                        <img src="{{ $user->profile_image }}" alt="profile image"
                            class="rounded-circle img-fluid profile-image p-2 bg-white" />
                    </div>

                    <!-- Contenedor del usuario -->
                    <div class="border container-user position-relative p-3 pt-5 ">
                        <br>
                        <div class="">
                            <p class="text-18 text-center mt-5 pt-5"> <img class="img-fluid me-2"
                                    src="{{ asset('images/icons/location.svg') }}" alt="location">{{ $user->address }}
                            </p>
                        </div>

                        <h3 class="text-center ">{{ $user->full_name }}</h3>

                        <div class="form-group p-3 border-bottom">
                            <label class="form-label" for="name">Sobre {{ $user->full_name }}</label>
                            @if ($user->profile)
                                @if (!is_null($user->profile->about_self))
                                    <p>{{ $user->profile->about_self }}</p>
                                @endif

                                @if (!is_null($user->profile->speciality_id))
                                    <p style="font-weight: bold;">Área de especialización</p>
                                    <p>{{ $user->profile->speciality->description }}</p>
                                @endif
                                <label class="form-label">Correo: {{ $user->email }}</label>
                                <br>
                                <label class="form-label">Género:
                                    {{ $user->gender == 'Male' ? 'Hombre' : 'Mujer' }}</label>
                                    @if ($user->mobile)
                                        <br>
                                        <label class="form-label">Teléfono: {{$user->mobile}}</label>
                                    @endif
                            @endif

                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>




    <script src="{{ mix('js/backend.js') }}"></script>
    {{-- <script src="{{ mix('js/iqonic-script/setting.min.js') }}"></script> --}}
    <script src="{{ asset('js/iqonic-script/utility.js') }}"></script>
    {{-- <script src="{{ asset('js/setting-init.js') }}"></script> --}}
    <script src="{{ asset('js/app.js') }}"></script>

    <script src="{{ mix('js/booking-form.min.js') }}"></script>


</body>

</html>
