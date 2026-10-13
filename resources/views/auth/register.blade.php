@extends('layouts.auth', ['title' => __('auth.register_title', ['default' => 'Create Account'])])

@push('scripts')
    @vite('resources/js/pages/auth/auth-form.js')
@endpush

@section('content')
    <div class="h-screen overflow-hidden">
        <div class="grid lg:grid-cols-5 md:grid-cols-2 items-center gap-y-4 h-full">
            <div class="md:col-span-2 w-full h-full flex items-center justify-center p-8 overflow-hidden">
                <form action="{{ route('register') }}" method="POST" data-auth-form
                    class="w-full max-w-lg max-h-full overflow-y-auto">
                    @csrf
                    <div class="mb-8">
                        <div class="flex justify-between items-center">
                            <div class="mb-6 flex items-center gap-4">
                                <a href="{{ route('login') }}"
                                    class="inline-flex items-center gap-2 text-sm font-medium text-(--color-dark) dark:text-(--color-light) hover:text-(--color-primary) transition">
                                    <i data-lucide="arrow-left" class="size-6"></i>
                                </a>
                                <h1 class="text-3xl font-bold text-(--color-dark) dark:text-(--color-light)">
                                    {{ __('auth.register', ['default' => 'Sign Up']) }}</h1>
                            </div>
                            <div class="flex items-center gap-4">
                                @if(View::exists('components.toggle-language'))
                                    @include('components.toggle-language')
                                @endif
                                @if(View::exists('components.toggle-theme'))
                                    @include('components.toggle-theme')
                                @endif
                            </div>
                        </div>
                        <p class="text-[15px] mt-2 text-(--color-dark) dark:text-(--color-light)">
                            {{ __('auth.already_registered', ['default' => 'Already have an account?']) }}
                            <a href="{{ route('login') }}" class="font-medium hover:underline ml-1 whitespace-nowrap text-(--color-primary)">
                                {{ __('auth.sign_in', ['default' => 'Sign In']) }}
                            </a>
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-[15px] font-medium mb-1 block text-(--color-dark) dark:text-(--color-light)"
                                for="name">{{ __('auth.fields.name', ['default' => 'Full Name']) }}</label>
                            <input type="text" name="name" id="name" autocomplete="name" required
                                class="py-2.5 px-4 block w-full bg-(--color-light) dark:bg-(--color-dark) text-(--color-dark) dark:text-(--color-light) border border-(--color-gray) rounded-lg sm:text-sm focus:border-(--color-primary) focus:ring-(--color-primary)"
                                placeholder="Your Name" value="{{ old('name') }}">
                            @error('name')
                                <p class="mt-1 text-sm text-(--color-danger)">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-[15px] font-medium mb-1 block text-(--color-dark) dark:text-(--color-light)"
                                for="email">{{ __('auth.fields.email', ['default' => 'Email Address']) }}</label>
                            <input type="email" name="email" id="email" autocomplete="email" required
                                class="py-2.5 px-4 block w-full bg-(--color-light) dark:bg-(--color-dark) text-(--color-dark) dark:text-(--color-light) border border-(--color-gray) rounded-lg sm:text-sm focus:border-(--color-primary) focus:ring-(--color-primary)"
                                placeholder="user@example.com" value="{{ old('email') }}">
                            @error('email')
                                <p class="mt-1 text-sm text-(--color-danger)">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-data="{ show: false }">
                            <label for="password" class="text-[15px] font-medium mb-1 block text-(--color-dark) dark:text-(--color-light)">
                                {{ __('auth.fields.password', ['default' => 'Password']) }}
                            </label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password" id="password" required
                                    class="py-2.5 px-4 pe-10 block w-full bg-(--color-light) dark:bg-(--color-dark) text-(--color-dark) dark:text-(--color-light) border border-(--color-gray) rounded-lg sm:text-sm focus:border-(--color-primary) focus:ring-(--color-primary)"
                                    placeholder="••••••••">
                                <button type="button" @click="show = !show" tabindex="-1"
                                    class="absolute inset-y-0 inset-e-3 flex items-center text-(--color-primary)/80 cursor-pointer">
                                    <i x-show="!show" data-lucide="eye" class="size-5"></i>
                                    <i x-show="show" data-lucide="eye-off" class="size-5"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-(--color-danger)">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-data="{ show: false }">
                            <label for="password_confirmation" class="text-[15px] font-medium mb-1 block text-(--color-dark) dark:text-(--color-light)">
                                {{ __('auth.fields.password_confirmation', ['default' => 'Confirm Password']) }}
                            </label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required
                                    class="py-2.5 px-4 pe-10 block w-full bg-(--color-light) dark:bg-(--color-dark) text-(--color-dark) dark:text-(--color-light) border border-(--color-gray) rounded-lg sm:text-sm focus:border-(--color-primary) focus:ring-(--color-primary)"
                                    placeholder="••••••••">
                                <button type="button" @click="show = !show" tabindex="-1"
                                    class="absolute inset-y-0 inset-e-3 flex items-center text-(--color-primary)/80 cursor-pointer">
                                    <i x-show="!show" data-lucide="eye" class="size-5"></i>
                                    <i x-show="show" data-lucide="eye-off" class="size-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <x-button-loading type="submit" text="{{ __('auth.register', ['default' => 'Register']) }}"
                            loadingText="Creating Account..."
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
