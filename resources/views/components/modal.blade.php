@props(['id', 'title' => null, 'size' => 'md', 'scrollable' => true, 'centered' => false, 'closeable' => true])
@php
    $sizeMap = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
        '5xl' => 'sm:max-w-5xl',
    ];
    $maxWidth = $sizeMap[$size] ?? $sizeMap['md'];
    $bodyOverflowClass = $scrollable ? 'overflow-y-auto' : 'overflow-visible';
@endphp
<div id="{{ $id }}"
    class="hs-overlay hidden size-full fixed top-0 inset-s-0 z-80 overflow-hidden pointer-events-none
        {{ $centered ? 'flex items-center justify-center' : '' }}"
    @if (!$closeable)
        style="--overlay-backdrop: static;"
        data-hs-overlay-keyboard="false"
        data-hs-overlay-options='{"backdrop": "static"}'
    @endif
    role="dialog" tabindex="-1" aria-labelledby="{{ $id }}-label">
    <div
        class="hs-overlay-animation-target hs-overlay-open:opacity-100 hs-overlay-open:duration-500
        opacity-0 ease-out transition-all {{ $maxWidth }} sm:w-full m-3 sm:mx-auto max-h-[80vh] flex flex-col
        {{ $centered ? '' : 'hs-overlay-open:mt-7 mt-0' }}">
        <div
            class="flex flex-col bg-(--color-light) border border-(--color-light-gray) shadow-2xs rounded-xl pointer-events-auto
            dark:bg-(--color-dark) dark:border-(--color-slate) max-h-full overflow-hidden">
            @if ($title)
                <div
                    class="flex justify-between items-center py-3 px-4 border-b border-(--color-light-gray) dark:border-(--color-slate) shrink-0">
                    <h3 id="{{ $id }}-label"
                        class="font-semibold text-(--color-dark) dark:text-(--color-light)">
                        {{ $title }}
                    </h3>
                    @if ($closeable)
                        <button type="button"
                            class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full
                                bg-(--color-light-gray) border border-(--color-gray)
                                text-(--color-dark) hover:bg-(--color-gray)/40
                                dark:bg-(--color-dark-slate) dark:border-(--color-slate) dark:text-(--color-light)
                                focus:outline-hidden disabled:opacity-50 disabled:pointer-events-none cursor-pointer"
                            aria-label="Close" data-hs-overlay="#{{ $id }}">
                            <span class="sr-only">Close</span>
                            <i data-lucide="x" class="text-(--color-dark)/80 dark:text-(--color-light)/80 size-5"></i>
                        </button>
                    @endif
                </div>
            @endif
            <div class="p-4 flex-1 min-h-0 {{ $bodyOverflowClass }} space-y-4">
                {{ $slot }}
            </div>
            @isset($footer)
                <div class="flex gap-2 p-4 border-t border-(--color-gray)/20 shrink-0">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
