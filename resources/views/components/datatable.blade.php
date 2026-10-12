@props([
    'id' => 'datatable',
    'search' => true,
    'length' => true,
    'filter' => true,
    'filterId' => 'dt-filter',
    'filterOptions' => [
        'all' => \Illuminate\Support\Facades\Lang::has('datatable.All') ? __('datatable.All') : (\Illuminate\Support\Facades\Lang::has('renkit.all') ? __('renkit.all') : 'Semua'),
        'active' => \Illuminate\Support\Facades\Lang::has('datatable.Active') ? __('datatable.Active') : (\Illuminate\Support\Facades\Lang::has('renkit.active') ? __('renkit.active') : 'Aktif'),
        'deleted' => \Illuminate\Support\Facades\Lang::has('datatable.Deleted') ? __('datatable.Deleted') : (\Illuminate\Support\Facades\Lang::has('renkit.deleted') ? __('renkit.deleted') : 'Terhapus'),
    ],
    'filterDefault' => 'active',
    'lengthOptions' => [10, 20, 50, -1],
    'defaultLength' => 10,
])

<div class="flex flex-col gap-3 mb-4 sm:flex-row sm:justify-between sm:items-center" data-dt-controls="{{ $id }}">
    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
        @if ($search)
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 inset-s-0 flex items-center pointer-events-none ps-4 z-10">
                    <i data-lucide="search" class="size-4"></i>
                </div>
                <input type="text" id="dt-search-{{ $id }}"
                    class="w-full ps-10 pe-10 py-2 px-3 text-sm rounded-lg
                    text-(--color-dark) dark:text-(--color-light)
                    border border-(--color-gray) dark:border-(--color-dark-gray)
                    bg-(--color-light) dark:bg-(--color-dark)
                    focus:ring-2 focus:ring-(--color-primary)/30"
                    placeholder="{{ \Illuminate\Support\Facades\Lang::has('datatable.Search...') ? __('datatable.Search...') : (\Illuminate\Support\Facades\Lang::has('renkit.search') ? __('renkit.search') : 'Cari data...') }}">
                <button type="button" id="dt-search-clear-{{ $id }}"
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
                    <option value="{{ $value }}" @selected($value === $filterDefault)>{{ $label }}</option>
                @endforeach
            </select>
        @endif
    </div>
</div>

<div class="w-full overflow-x-auto rounded-xl border border-(--color-light-gray) dark:border-(--color-slate) bg-(--color-light) dark:bg-(--color-dark)">
    <table id="{{ $id }}" class="w-full text-sm text-left border-collapse">
        {{ $slot }}
    </table>
</div>
