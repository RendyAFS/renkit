<div class="hs-dropdown inline-flex">
    <button id="hs-dropdown-locale" type="button"
        class="hs-dropdown-toggle inline-flex items-center gap-x-2 px-3 py-2 text-sm font-medium rounded-lg hover:bg-(--color-gray)/20 dark:hover:bg-(--color-gray)/20 cursor-pointer">
        <i data-lucide="globe" class="size-4 text-(--color-dark-gray)"></i>
        <span class="text-(--color-dark-gray) dark:text-(--color-gray)">
            {{ strtoupper(app()->getLocale()) }}
        </span>
        <i data-lucide="chevron-down"
            class="hs-dropdown-open:rotate-180 size-4 transition-transform text-(--color-dark-gray)"></i>
    </button>

    <div class="hs-dropdown-menu hs-dropdown-open:opacity-100 mt-2 hidden z-10 transition-[margin,opacity] opacity-0 duration-300 min-w-32 bg-(--color-light) dark:bg-(--color-dark) dark:border dark:border-(--color-gray)/30 shadow-md rounded-lg p-2"
        role="menu">

        <a href="{{ route('locale.switch', 'id') }}"
            class="flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm {{ app()->getLocale() === 'id' ? 'text-(--color-primary) font-semibold' : 'text-(--color-dark)' }} dark:text-(--color-gray) hover:bg-(--color-gray)/20">
            Indonesia
        </a>

        <a href="{{ route('locale.switch', 'en') }}"
            class="flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm {{ app()->getLocale() === 'en' ? 'text-(--color-primary) font-semibold' : 'text-(--color-dark)' }} dark:text-(--color-gray) hover:bg-(--color-gray)/20">
            English
        </a>
    </div>
</div>
