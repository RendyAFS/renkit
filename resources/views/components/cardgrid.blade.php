@props([
    'id' => 'cardgrid',
    'search' => true,
    'length' => true,
    'filter' => true,
    'filterId' => 'cg-filter',
    'filterOptions' => [
        'all' => \Illuminate\Support\Facades\Lang::has('cardgrid.All') ? __('cardgrid.All') : (\Illuminate\Support\Facades\Lang::has('renkit.all') ? __('renkit.all') : 'Semua'),
        'active' => \Illuminate\Support\Facades\Lang::has('cardgrid.Active') ? __('cardgrid.Active') : (\Illuminate\Support\Facades\Lang::has('renkit.active') ? __('renkit.active') : 'Aktif'),
        'deleted' => \Illuminate\Support\Facades\Lang::has('cardgrid.Deleted') ? __('cardgrid.Deleted') : (\Illuminate\Support\Facades\Lang::has('renkit.deleted') ? __('renkit.deleted') : 'Terhapus'),
    ],
    'filterDefault' => 'active',
    'lengthOptions' => [12, 24, 48],
    'defaultLength' => 12,
    'filtersInline' => false,
    'gridCols' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
])

{{-- Top Controls --}}
<div class="flex flex-col gap-3 mb-4" data-cg-controls="{{ $id }}">
    <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
        <div class="flex flex-wrap items-center gap-3 w-full sm:flex-1 sm:min-w-0">
            @if ($search)
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 inset-s-0 flex items-center pointer-events-none ps-4 z-10">
                        <i data-lucide="search" class="size-4"></i>
                    </div>

                    <input type="text" id="cg-search"
                        class="w-full ps-10 pe-10 py-2 px-3 text-sm rounded-lg
                        text-(--color-dark) dark:text-(--color-light)
                        border border-(--color-gray) dark:border-(--color-dark-gray)
                        bg-(--color-light) dark:bg-(--color-dark-slate)
                        focus:ring-2 focus:ring-(--color-primary)/30"
                        placeholder="{{ \Illuminate\Support\Facades\Lang::has('cardgrid.Search...') ? __('cardgrid.Search...') : (\Illuminate\Support\Facades\Lang::has('renkit.search') ? __('renkit.search') : 'Cari data...') }}">

                    <button type="button" id="cg-search-clear"
                        class="absolute inset-y-0 inset-e-0 hidden items-center pe-3
                            text-(--color-gray) hover:text-(--color-dark) dark:hover:text-(--color-light)
                            transition cursor-pointer">
                        <i data-lucide="x" class="size-4"></i>
                    </button>
                </div>
            @endif

            @if ($filter)
                <select id="{{ $filterId }}" class="w-auto sm:w-40 py-2 px-3 text-sm rounded-lg border border-(--color-gray) dark:border-(--color-slate) bg-(--color-light) dark:bg-(--color-dark) text-(--color-dark) dark:text-(--color-light)">
                    @foreach ($filterOptions as $value => $label)
                        <option value="{{ $value }}" @selected($value == $filterDefault)>{{ $label }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        @if ($length)
            <div class="flex items-center gap-2 w-auto sm:w-auto justify-end">
                <span class="text-sm whitespace-nowrap text-(--color-dark-gray)">
                    {{ \Illuminate\Support\Facades\Lang::has('cardgrid.Show') ? __('cardgrid.Show') : (\Illuminate\Support\Facades\Lang::has('renkit.show') ? __('renkit.show') : 'Tampilkan') }}
                </span>
                <select id="cg-length" class="w-auto sm:w-24 py-2 px-3 text-sm rounded-lg border border-(--color-gray) dark:border-(--color-slate) bg-(--color-light) dark:bg-(--color-dark) text-(--color-dark) dark:text-(--color-light)">
                    @foreach ($lengthOptions as $opt)
                        <option value="{{ $opt }}" @selected($opt == $defaultLength)>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>
</div>

{{-- Card Grid Container --}}
<div class="relative min-h-40">
    <div id="{{ $id }}-loading" class="hidden">
        <div class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-[2px]">
            <div class="flex flex-col items-center gap-3">
                <div class="size-10 rounded-full border-4 border-(--color-primary)/20 border-t-(--color-primary) animate-spin"></div>
                <span class="text-sm text-(--color-dark) dark:text-(--color-light)">{{ \Illuminate\Support\Facades\Lang::has('cardgrid.Loading data...') ? __('cardgrid.Loading data...') : (\Illuminate\Support\Facades\Lang::has('renkit.loading') ? __('renkit.loading') : 'Memuat data...') }}</span>
            </div>
        </div>
    </div>

    <div id="{{ $id }}" class="grid {{ $gridCols }} gap-4 transition duration-200">
        {{-- Cards injected by JS --}}
    </div>
</div>

{{-- Empty State --}}
<div id="{{ $id }}-empty" class="hidden">
    <div class="flex flex-col items-center justify-center py-20 text-(--color-gray)">
        <i data-lucide="inbox" class="size-12 mb-3"></i>
        <p class="text-sm text-(--color-dark-gray) mt-1">{{ \Illuminate\Support\Facades\Lang::has('cardgrid.No data found') ? __('cardgrid.No data found') : (\Illuminate\Support\Facades\Lang::has('renkit.empty') ? __('renkit.empty') : 'Tidak ada data ditemukan') }}</p>
    </div>
</div>

{{-- Pagination Footer --}}
<div class="flex flex-col gap-3 mt-4 sm:flex-row sm:justify-between sm:items-center">
    <div id="cg-info" class="text-sm text-center sm:text-left text-(--color-dark-gray)"></div>
    <div id="cg-pagination" class="flex flex-wrap justify-center gap-1 sm:justify-end"></div>
</div>

<template id="cg-pagination-btn-template">
    <button type="button"
        class="min-h-9 min-w-9 flex justify-center items-center text-sm rounded-lg focus:outline-hidden disabled:opacity-50 disabled:pointer-events-none cursor-pointer"
        data-page=""></button>
</template>
<template id="cg-pagination-ellipsis-template">
    <span class="min-h-9 min-w-9 flex justify-center items-center text-sm text-(--color-gray)">…</span>
</template>
<template id="cg-pagination-prev-template">
    <button type="button"
        class="min-h-9 min-w-9 flex justify-center items-center text-sm rounded-lg focus:outline-hidden disabled:opacity-50 disabled:pointer-events-none border border-transparent cursor-pointer"
        data-page="" aria-label="{{ \Illuminate\Support\Facades\Lang::has('cardgrid.Previous') ? __('cardgrid.Previous') : (\Illuminate\Support\Facades\Lang::has('renkit.previous') ? __('renkit.previous') : 'Sebelumnya') }}">
        <i data-lucide="chevron-left" class="size-4"></i>
    </button>
</template>
<template id="cg-pagination-next-template">
    <button type="button"
        class="min-h-9 min-w-9 flex justify-center items-center text-sm rounded-lg focus:outline-hidden disabled:opacity-50 disabled:pointer-events-none border border-transparent cursor-pointer"
        data-page="" aria-label="{{ \Illuminate\Support\Facades\Lang::has('cardgrid.Next') ? __('cardgrid.Next') : (\Illuminate\Support\Facades\Lang::has('renkit.next') ? __('renkit.next') : 'Berikutnya') }}">
        <i data-lucide="chevron-right" class="size-4"></i>
    </button>
</template>
