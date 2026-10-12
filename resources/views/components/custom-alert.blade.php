@props([
    'top' => 'top-22',
    'right' => 'right-4',
])

<div x-data="customAlert()"
    x-init="
        document.addEventListener('toast', (e) => { showToast(e.detail.message, e.detail.type, e.detail.timeout, e.detail.title) });
        document.addEventListener('alert', (e) => { showAlert(e.detail.title, e.detail.message, e.detail.type, e.detail.confirmText) });
        document.addEventListener('confirm', (e) => { showConfirm(e.detail.title, e.detail.message, e.detail.confirmText, e.detail.cancelText, e.detail.onConfirm, e.detail.onCancel) });
    ">

    {{-- Toast Container --}}
    <div class="fixed {{ $top }} {{ $right }} z-50 flex flex-col gap-3 w-max max-w-xs">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.show" @mouseenter="pauseToast(toast.id)" @mouseleave="resumeToast(toast.id)"
                x-transition:enter="transition transform ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition transform ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-3"
                class="w-80 rounded-xl shadow-lg bg-(--color-light) dark:bg-(--color-dark) border border-(--color-gray)"
                role="alert">

                <div class="flex items-start gap-3 px-6 py-4">
                    <!-- Icon + Message -->
                    <div class="flex items-center gap-4 flex-1">
                        <div class="shrink-0" x-html="icons[toast.type]"></div>
                        <div class="text-sm text-(--color-dark) dark:text-(--color-light)">
                            <p class="font-semibold leading-tight" x-text="toast.title"></p>
                            <p class="mt-0.5 text-sm opacity-90" x-text="toast.message"></p>
                        </div>
                    </div>

                    <!-- Close Button -->
                    <button @click="removeToast(toast.id)"
                        class="ms-3 p-2 rounded-full text-(--color-dark) dark:text-(--color-light) bg-(--color-gray)/20
                               hover:bg-(--color-gray)/40 transition ease-in-out duration-200 cursor-pointer">
                        <i data-lucide="x" class="text-(--color-dark)/70 dark:text-(--color-light)/70 size-4"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
    window.renkitLang = Object.assign(window.renkitLang || {}, {
        success: @json(\Illuminate\Support\Facades\Lang::has('custom-alert.Success') ? __('custom-alert.Success') : (\Illuminate\Support\Facades\Lang::has('renkit.success') ? __('renkit.success') : 'Sukses')),
        info: @json(\Illuminate\Support\Facades\Lang::has('custom-alert.Information') ? __('custom-alert.Information') : (\Illuminate\Support\Facades\Lang::has('renkit.info') ? __('renkit.info') : 'Informasi')),
        warning: @json(\Illuminate\Support\Facades\Lang::has('custom-alert.Warning') ? __('custom-alert.Warning') : (\Illuminate\Support\Facades\Lang::has('renkit.warning') ? __('renkit.warning') : 'Peringatan')),
        error: @json(\Illuminate\Support\Facades\Lang::has('custom-alert.Error') ? __('custom-alert.Error') : (\Illuminate\Support\Facades\Lang::has('renkit.error') ? __('renkit.error') : 'Error')),
        confirm: @json(\Illuminate\Support\Facades\Lang::has('custom-alert.Confirm') ? __('custom-alert.Confirm') : (\Illuminate\Support\Facades\Lang::has('renkit.confirm') ? __('renkit.confirm') : 'Konfirmasi')),
        cancel: @json(\Illuminate\Support\Facades\Lang::has('custom-alert.Cancel') ? __('custom-alert.Cancel') : (\Illuminate\Support\Facades\Lang::has('renkit.cancel') ? __('renkit.cancel') : 'Batal')),
        delete_confirm: @json(\Illuminate\Support\Facades\Lang::has('custom-alert.Are you sure you want to delete this item? This action cannot be undone.') ? __('custom-alert.Are you sure you want to delete this item? This action cannot be undone.') : (\Illuminate\Support\Facades\Lang::has('renkit.delete_confirm') ? __('renkit.delete_confirm') : 'Apakah Anda yakin ingin menghapus data ini?')),
    });
</script>
