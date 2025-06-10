<x-auth-layout>
    <x-slot name="title">
        @lang('Login')
    </x-slot>

    <div class="container-fluid min-vh-100 d-flex flex-column justify-content-center">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 align-items-center align-content-center">
                <x-auth-card>

                    <x-slot name="logo">
                        <a href="/">
                            <x-application-logo />
                        </a>
                    </x-slot>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <!-- Social Login -->
                    <x-auth-social-login />



                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ $url ?? route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <x-label for="email" :value="__('Correo')" />

                            <x-input id="email" type="email" name="email" value="" required autofocus />
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <x-label for="password" :value="__('Contraseña')" />

                            <x-input id="password" type="password" name="password" required
                                autocomplete="current-password" />
                        </div>

                        <!-- Remember Me -->
                        <div class="mt-4">
                            <label for="remember_me" class="d-inline-flex">
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span class="ms-2">{{ __('Recuérdame') }}</span>
                            </label>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-4">
                            @if (Route::has('password.request'))
                                <a class="underline text-sm text-gray-600 hover:text-gray-900"
                                    href="{{ route('password.request') }}">
                                    {{ __('¿Olvidaste tu contraseña?') }}
                                </a>
                            @endif

                            <x-button>
                                {{ __('Iniciar sesión') }}
                            </x-button>
                        </div>

                    </form>

            </div>

            <x-slot name="extra">
                @if (Route::has('register'))
                    <p class="text-center text-gray-600 mt-4">
                        ¿No tienes una cuenta? <a href="{{ route('register') }}"
                            class="underline hover:text-gray-900">Registrarse</a>.
                    </p>
                    <p class="text-center text-gray-600 mt-4">
                @endif
            </x-slot>

            </x-auth-card>

        </div>
    </div>
    </div>

    <script type="text/javascript">
        //  window.onload = function() {
        //      getSelectedOption();
        // };

        function getSelectedOption() {
            var selectElement = document.getElementById("SelectUser");
            var selectedOption = selectElement.options[selectElement.selectedIndex];

            if (selectedOption) {
                var optionText = selectedOption.textContent || selectedOption
                    .innerText; // Get the text of the selected option
                var optionValue = selectedOption.value; // Get the value of the selected option

                var values = optionValue.split(",");
                var password = values[0];
                var email = values[1];

                domId('email').value = email;
                domId('password').value = password;

                // domId('email').value =optionText;
                // domId('password').value = optionValue;

            } else {

            }
        }



        function domId(name) {
            return document.getElementById(name)
        }

        function setLoginCredentials(type) {
            domId('email').value = domId(type + '_email').textContent
            domId('password').value = domId(type + '_password').textContent
        }
    </script>

</x-auth-layout>
