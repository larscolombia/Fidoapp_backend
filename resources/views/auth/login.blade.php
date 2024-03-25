<x-auth-layout>
    <x-slot name="title">
        @lang('Login')
    </x-slot>

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
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" type="email" name="email" value="" required autofocus />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Contraseña')" />

                <x-input id="password" type="password" name="password" required autocomplete="current-password" />
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
        <div>
            <h6 class="text-center border-top py-3 mt-3">Cuentas de demostración</h6>
            <div class="parent">

                <select name="select" id="SelectUser" id="select" class="form-control selectpiker"
                    onchange="getSelectedOption()">
                    <option value="12345678,demo@pawlly.com" selected>Demo de Administrador</option>
                    <option value="12345678,miles@gmail.com">Internado</option>
                    <option value="12345678,felix@gmail.com">Veterinario</option>
                    <option value="12345678,richard@gmail.com">Peluquero</option>
                    <option value="12345678,tristan@gmail.com">Entrenador</option>
                    <option value="12345678,pedro@gmail.com">Paseador</option>
                    <option value="12345678,justin@gmail.com">Cuidador de guardería</option>
                    <option value="12345678,harry@gmail.com">Cuidador de mascotas</option>
                </select>

            </div>

        </div>
        </div>

        <x-slot name="extra">
            @if (Route::has('register'))
                <p class="text-center text-gray-600 mt-4">
                    ¿No tienes una cuenta? <a href="{{ route('register') }}"
                        class="underline hover:text-gray-900">Registrarse</a>.
                </p>
            @endif
        </x-slot>
    </x-auth-card>


    <script type="text/javascript">
        window.onload = function() {
            getSelectedOption();
        };

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
