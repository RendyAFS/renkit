<header class="sticky top-0 inset-x-0 z-50 flex flex-wrap md:justify-start md:flex-nowrap bg-(--color-light) dark:bg-(--color-dark) border-b border-(--color-gray)/20 text-sm py-2.5">
    <nav class="max-w-[85rem] basis-full mx-auto w-full flex items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-x-3">
            <!-- Sidebar Toggle (Mobile) -->
            <button type="button" class="lg:hidden text-(--color-dark) dark:text-(--color-light) p-2 rounded-lg hover:bg-(--color-gray)/20 cursor-pointer"
                data-hs-overlay="#hs-sidebar-content-push" aria-controls="hs-sidebar-content-push" aria-label="Toggle navigation">
                <i data-lucide="menu" class="size-5"></i>
            </button>

            <!-- Brand / Logo for mobile -->
            <a class="flex items-center gap-2 lg:hidden font-semibold text-lg text-(--color-primary)" href="{{ url('/') }}">
                <span>{{ config('app.name', 'RenKit') }}</span>
            </a>
        </div>

        <div class="flex items-center gap-x-2 md:gap-x-4">
            <!-- Theme Toggle -->
            @if(View::exists('components.toggle-theme'))
                @include('components.toggle-theme')
            @endif

            <!-- Language Toggle -->
            @if(View::exists('components.toggle-language'))
                @include('components.toggle-language')
            @endif

            <!-- User Menu -->
            @auth
                <div class="hs-dropdown relative inline-flex">
                    <button id="hs-dropdown-avatar" type="button"
                        class="hs-dropdown-toggle inline-flex items-center gap-x-2 text-sm font-semibold rounded-full p-1 text-(--color-dark) dark:text-(--color-light) hover:bg-(--color-gray)/20 cursor-pointer">
                        <div class="size-8 rounded-full bg-(--color-primary) text-white flex items-center justify-center font-bold text-xs uppercase shadow-sm">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <span class="hidden md:inline-block max-w-32 truncate">{{ auth()->user()->name }}</span>
                        <i data-lucide="chevron-down" class="size-4 text-(--color-dark-gray)"></i>
                    </button>

                    <div class="hs-dropdown-menu hs-dropdown-open:opacity-100 min-w-48 hidden z-20 transition-[margin,opacity] opacity-0 duration-300 mt-2 bg-(--color-light) dark:bg-(--color-dark) border border-(--color-gray)/20 shadow-lg rounded-xl p-2"
                        role="menu" aria-orientation="vertical">
                        <div class="py-2 px-3 border-b border-(--color-gray)/20">
                            <p class="text-xs text-(--color-dark-gray)">Signed in as</p>
                            <p class="text-sm font-semibold truncate">{{ auth()->user()->email }}</p>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="mt-1">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-x-2 py-2 px-3 rounded-lg text-sm text-(--color-danger) hover:bg-(--color-danger)/10 transition cursor-pointer">
                                <i data-lucide="log-out" class="size-4"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-(--color-primary) text-white hover:bg-(--color-primary)/80 transition">
                        Sign In
                    </a>
                @endif
            @endauth
        </div>
    </nav>
</header>
