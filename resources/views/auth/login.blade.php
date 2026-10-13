@extends('layouts.auth', ['title' => __('auth.login_title', ['default' => 'Sign In'])])

@push('scripts')
    @vite('resources/js/pages/auth/auth-form.js')
    @if ($errors->any())
        <script>
            const errors = @json($errors->all());

            errors.forEach((msg, i) => {
                sessionStorage.setItem(
                    "flash_toast_" + i,
                    JSON.stringify({
                        type: "error",
                        title: "Login Failed",
                        message: msg,
                        timeout: 5000
                    })
                );
            });
        </script>
    @endif
@endpush

@section('content')
    <div class="h-screen overflow-hidden">
        <div class="grid lg:grid-cols-5 md:grid-cols-2 items-center gap-y-4 h-full">
            <div class="md:col-span-2 w-full h-full flex items-center justify-center p-8 overflow-hidden">
                <form action="{{ route('login') }}" method="POST" data-auth-form
                    class="w-full max-w-lg max-h-full overflow-y-auto">
                    @csrf
                    <div class="mb-8">
                        <div class="flex justify-between items-center">
                            <div class="mb-6 flex items-center gap-4">
                                <a href="{{ url('/') }}"
                                    class="inline-flex items-center gap-2 text-sm font-medium text-(--color-dark) dark:text-(--color-light) hover:text-(--color-primary) transition">
                                    <i data-lucide="arrow-left" class="size-6"></i>
                                </a>
                                <h1 class="text-3xl font-bold text-(--color-dark) dark:text-(--color-light)">
                                    {{ __('auth.sign_in', ['default' => 'Sign In']) }}</h1>
                            </div>
                            <div class="flex items-center gap-4">
                                @if(Route::has('locale.switch') && View::exists('components.toggle-language'))
                                    @include('components.toggle-language')
                                @endif
                                @if(View::exists('components.toggle-theme'))
                                    @include('components.toggle-theme')
                                @endif
                            </div>
                        </div>
                        <p class="text-[15px] mt-2 text-(--color-dark) dark:text-(--color-light)">
                            {{ __('auth.no_account', ['default' => "Don't have an account?"]) }}
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="font-medium hover:underline ml-1 whitespace-nowrap text-(--color-primary)">
                                    {{ __('auth.register_here', ['default' => 'Register here']) }}
                                </a>
                            @endif
                        </p>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="text-[15px] font-medium mb-2 block text-(--color-dark) dark:text-(--color-light)"
                                for="email">{{ __('auth.fields.email', ['default' => 'Email Address']) }}</label>
                            <div class="relative">
                                <input type="text" name="email" id="email" autocomplete="email" required
                                    class="peer py-2.5 sm:py-3 px-4 ps-4 block w-full
                                    bg-(--color-light) dark:bg-(--color-dark) text-(--color-dark) dark:text-(--color-light)
                                    border rounded-lg sm:text-sm
                                    {{ $errors->has('email')
                                        ? 'border-(--color-red) focus:border-(--color-red) focus:ring-(--color-red)'
                                        : 'border-(--color-primary) focus:border-(--color-primary) focus:ring-(--color-primary)' }}
                                    disabled:opacity-50 disabled:pointer-events-none"
                                    placeholder="user@example.com" value="{{ old('email') }}">
                                <div class="absolute inset-y-0 inset-e-4 flex items-center pointer-events-none">
                                    <i data-lucide="mail" class="text-(--color-primary)/80 size-5"></i>
                                </div>
                            </div>
                            @error('email')
                                <p class="mt-1 text-sm text-(--color-danger)">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-data="{ show: false }">
                            <label class="text-[15px] font-medium mb-2 block text-(--color-dark) dark:text-(--color-light)"
                                for="password">
                                {{ __('auth.fields.password', ['default' => 'Password']) }}
                            </label>

                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password" id="password" required
                                    autocomplete="current-password"
                                    class="peer py-2.5 sm:py-3 px-4 ps-4 block w-full
                                        bg-(--color-light) dark:bg-(--color-dark) text-(--color-dark) dark:text-(--color-light)
                                        border rounded-lg sm:text-sm
                                        {{ $errors->has('password')
                                            ? 'border-(--color-red) focus:border-(--color-red) focus:ring-(--color-red)'
                                            : 'border-(--color-primary) focus:border-(--color-primary) focus:ring-(--color-primary)' }}
                                        disabled:opacity-50 disabled:pointer-events-none"
                                    placeholder="••••••••">

                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 inset-e-4 flex items-center text-(--color-primary)/70
                                        hover:text-(--color-primary) transition cursor-pointer">
                                    <i x-show="!show" data-lucide="eye-off" class="size-5"></i>
                                    <i x-show="show" data-lucide="eye" class="size-5"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-(--color-danger)">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="remember" id="remember" value="1"
                                    class="checkbox-custom">
                                <label for="remember"
                                    class="text-sm text-(--color-dark) ms-3 dark:text-(--color-light)">
                                    {{ __('auth.remember_me', ['default' => 'Remember Me']) }}
                                </label>
                            </div>
                            @if (Route::has('password.request'))
                                <div>
                                    <a href="{{ route('password.request') }}" class="font-medium text-sm hover:underline text-(--color-primary)">
                                        {{ __('auth.forgot_password', ['default' => 'Forgot Password?']) }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8">
                        <x-button-loading type="submit" text="{{ __('auth.sign_in', ['default' => 'Sign In']) }}"
                            loadingText="Authenticating..."
                            color="bg-(--color-primary) hover:bg-(--color-primary)/80"
                            textColor="text-(--color-light)" size="w-full py-2.5 px-4 text-[15px]"
                            rounded="rounded-md" class="cursor-pointer" />
                    </div>
                </form>
            </div>

            @include('components.auth-slideshow')
        </div>
    </div>
@endsection
