@props(['menus' => []])
<ul class="space-y-1.5">
    @foreach ($menus as $rawMenu)
        @php
            $menu = is_array($rawMenu) ? (object) $rawMenu : $rawMenu;
            $rawChildren = $menu->children ?? [];
            $children = collect($rawChildren)->map(fn($c) => is_array($c) ? (object) $c : $c);
            $menuUrl = $menu->url ?? '#';
            $menuIcon = $menu->icon ?? 'box';
            $menuName = $menu->name ?? 'Menu';

            $isOpen = $children->contains(fn($child) => request()->is(trim($child->url ?? '', '/') . '*'));
            $isActive = $children->isEmpty() && request()->is(trim($menuUrl, '/') . '*');
        @endphp

        @if ($children->isEmpty())
            <li>
                <a href="{{ url($menuUrl) }}"
                    class="flex items-center gap-x-3.5 py-3 px-3.5 rounded-full text-sm font-semibold
                    transition-all duration-300 ease-in-out cursor-pointer
                    {{ $isActive
                        ? 'bg-(--color-primary) text-(--color-light) shadow-sm shadow-(--color-primary)/30'
                        : 'text-(--color-dark-gray) dark:text-(--color-light) hover:bg-(--color-gray)/30 hover:text-(--color-primary) dark:hover:text-(--color-secondary)' }}">
                    <i data-lucide="{{ $menuIcon }}" class="size-4 shrink-0"></i>
                    <span class="hs-overlay-minified:group-hover/sidebar:block hs-overlay-minified:hidden truncate">
                        {{ __($menuName) }}
                    </span>
                </a>
            </li>
        @else
            <li class="hs-accordion {{ $isOpen ? 'hs-accordion-active' : '' }}">
                <button type="button"
                    class="hs-accordion-toggle w-full flex items-center gap-x-3.5 py-3 px-3.5 rounded-full text-sm font-semibold
                    transition-all duration-300 ease-in-out cursor-pointer
                    text-(--color-dark-gray) dark:text-(--color-light)
                    hover:bg-(--color-gray)/30 hover:text-(--color-primary) dark:hover:text-(--color-secondary)">
                    <i data-lucide="{{ $menuIcon }}" class="size-4 shrink-0"></i>
                    <span class="hs-overlay-minified:group-hover/sidebar:block hs-overlay-minified:hidden truncate">
                        {{ __($menuName) }}
                    </span>
                    <i data-lucide="chevron-down"
                        class="ms-auto size-4 shrink-0 transition-transform duration-300
                        hs-accordion-active:rotate-180
                        hs-overlay-minified:group-hover/sidebar:block hs-overlay-minified:hidden"></i>
                </button>
                <div
                    class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ $isOpen ? '' : 'hidden' }}">
                    <ul class="mt-1 ps-7 space-y-1 hs-overlay-minified:group-hover/sidebar:block hs-overlay-minified:hidden">
                        @foreach ($children as $child)
                            @php
                                $childUrl = $child->url ?? '#';
                                $childName = $child->name ?? 'Submenu';
                                $isChildActive = request()->is(trim($childUrl, '/') . '*');
                            @endphp
                            <li>
                                <a href="{{ url($childUrl) }}"
                                    class="block py-2 px-3.5 rounded-full text-sm font-semibold truncate
                                    transition-all duration-300 ease-in-out
                                    {{ $isChildActive
                                        ? 'bg-(--color-primary) text-(--color-light) shadow-sm shadow-(--color-primary)/30'
                                        : 'text-(--color-dark-gray) dark:text-(--color-light) hover:bg-(--color-gray)/30 hover:text-(--color-primary) dark:hover:text-(--color-secondary)' }}">
                                    {{ __($childName) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </li>
        @endif
    @endforeach
</ul>
