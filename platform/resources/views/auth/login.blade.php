<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="sl-form-label">E-mail</label>
            <x-text-input id="email" class="ts-field-reset block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="sl-form-label">Heslo</label>

            <x-text-input id="password" class="ts-field-reset block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-500/60 bg-slate-800 text-rose-500 shadow-none focus:ring-0" name="remember">
                <span class="ms-2 text-sm font-medium text-slate-200">Zapamätať si ma</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="rounded-md text-sm font-medium text-slate-300 transition hover:text-white focus:outline-none" href="{{ route('password.request') }}">
                    Zabudli ste heslo?
                </a>
            @endif

            <x-primary-button class="ms-3">
                Prihlásiť sa
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
