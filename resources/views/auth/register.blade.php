<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Create your account</h1>
        <p class="text-sm text-gray-500 mt-1">It only takes a moment to get started.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @php($redirectTo = old('redirect_to', request('redirect_to')))
        @if($redirectTo)
            <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
        @endif
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1.5" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Jane Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1.5" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1.5"
                            type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1.5"
                            type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full py-3">
            {{ __('Create account') }}
        </x-primary-button>

        @php($loginRoute = $redirectTo ? route('login', ['redirect_to' => $redirectTo]) : route('login'))
        <p class="text-center text-sm text-gray-500">
            Already have an account?
            <a href="{{ $loginRoute }}" class="font-semibold text-accent hover:text-accent-dark">Log in</a>
        </p>
    </form>
</x-guest-layout>
