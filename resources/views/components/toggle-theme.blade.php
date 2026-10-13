<div class="hs-dropdown relative">
    <button id="hs-dropdown-dark-mode" type="button"
        class="hs-dropdown-toggle hs-dark-mode group flex items-center hover:cursor-pointer
        text-(--color-dark-gray) hover:text-(--color-primary)
        focus:outline-hidden focus:text-(--color-primary)
        font-medium dark:text-(--color-gray) dark:hover:text-(--color-gray) dark:focus:text-(--color-gray)"
        aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
        <!-- LIGHT -->
        <i data-lucide="sun" class="theme-icon theme-light block size-5"></i>
        <!-- DARK -->
        <i data-lucide="moon" class="theme-icon theme-dark hidden size-5"></i>
        <!-- SYSTEM -->
        <i data-lucide="monitor-cog" class="theme-icon theme-auto hidden size-5"></i>
    </button>

    <div id="selectThemeDropdown"
        class="hs-dropdown-menu hs-dropdown-open:opacity-100 mt-2 hidden z-10 transition-[margin,opacity]
        opacity-0 duration-300 mb-2 origin-bottom-left bg-(--color-light)
        shadow-md rounded-lg p-1 space-y-0.5 dark:bg-(--color-dark)
        dark:border dark:border-(--color-gray)/30"
        role="menu" aria-orientation="vertical" aria-labelledby="hs-dropdown-dark-mode">

        <!-- LIGHT -->
        <button type="button"
            class="w-full flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm hover:cursor-pointer
            text-(--color-dark) hover:bg-(--color-gray)/20
            dark:text-(--color-gray) dark:hover:bg-(--color-gray)/20"
            data-hs-theme-click-value="light">
            <i data-lucide="sun" class="size-4"></i>
            {{ __('navbar.Light') }}
        </button>

        <!-- DARK -->
        <button type="button"
            class="w-full flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm hover:cursor-pointer
            text-(--color-dark) hover:bg-(--color-gray)/20
            dark:text-(--color-gray) dark:hover:bg-(--color-gray)/20"
            data-hs-theme-click-value="dark">
            <i data-lucide="moon" class="size-4"></i>
            {{ __('navbar.Dark') }}
        </button>

        <!-- SYSTEM / AUTO -->
        <button type="button"
            class="w-full flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm hover:cursor-pointer
            text-(--color-dark) hover:bg-(--color-gray)/20
            dark:text-(--color-gray) dark:hover:bg-(--color-gray)/20"
            data-hs-theme-click-value="auto">
            <i data-lucide="monitor-cog" class="size-4"></i>
            {{ __('navbar.Auto (System)') }}
        </button>
    </div>
</div>
