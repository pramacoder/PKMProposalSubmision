<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Halo :name, silakan masukkan kata sandi baru untuk mengklaim akun Anda.', ['name' => $user->name]) }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    
    @if (session('error'))
        <div class="mb-4 text-sm font-medium text-red-600">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('claim.store', ['user' => $user->id, 'token' => $token]) }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full bg-gray-100" type="email" name="email" :value="$user->email" disabled autofocus autocomplete="username" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi Baru')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi Baru')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Klaim Akun') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
