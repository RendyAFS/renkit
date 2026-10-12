@props([
    'type' => 'button',
    'text' => \Illuminate\Support\Facades\Lang::has('button-loading.Save') ? __('button-loading.Save') : (\Illuminate\Support\Facades\Lang::has('renkit.save') ? __('renkit.save') : 'Simpan'),
    'loadingText' => \Illuminate\Support\Facades\Lang::has('button-loading.Saving...') ? __('button-loading.Saving...') : (\Illuminate\Support\Facades\Lang::has('renkit.saving') ? __('renkit.saving') : 'Menyimpan...'),
    'color' => 'bg-(--color-primary) hover:bg-(--color-primary)/80',
    'textColor' => 'text-(--color-light)',
    'size' => 'py-2.5 px-4 text-sm',
    'rounded' => 'rounded-lg',
    'icon' => null,
])

<button type="{{ $type }}" data-button-loading data-loading-text="{{ $loadingText }}"
    {{ $attributes->merge([
        'class' => "inline-flex items-center justify-center gap-x-2 font-medium transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none $size $rounded $color $textColor cursor-pointer",
    ]) }}>
    <span class="hidden animate-spin size-4 border-2 border-current border-t-transparent rounded-full" data-spinner
        aria-label="loading" role="status"></span>

    @if ($icon)
        <i data-lucide="{{ $icon }}" class="size-4" data-icon></i>
    @endif

    <span data-text>{{ $text }}</span>
</button>
