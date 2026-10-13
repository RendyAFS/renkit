const Loading = {
    _count: 0,

    start(message = "Loading...") {
        this._count++;
        if (this._count > 1) return;

        $.blockUI({
            message: `
                <div class="flex flex-col items-center justify-center gap-4">
                    <div class="inline-flex" role="status" aria-label="loading">
                        <span class="flex h-8 items-center justify-center gap-1" aria-hidden="true">
                            <span class="h-3 w-1.5 rounded-full bg-(--color-primary) origin-center animate-[spinner-wave_0.9s_ease-in-out_infinite_0.12s]"></span>
                            <span class="h-5 w-1.5 rounded-full bg-(--color-primary) origin-center animate-[spinner-wave_0.9s_ease-in-out_infinite_0.12s]"></span>
                            <span class="h-7 w-1.5 rounded-full bg-(--color-primary) origin-center animate-[spinner-wave_0.9s_ease-in-out_infinite_0.24s]"></span>
                            <span class="h-5 w-1.5 rounded-full bg-(--color-primary) origin-center animate-[spinner-wave_0.9s_ease-in-out_infinite_0.36s]"></span>
                            <span class="h-3 w-1.5 rounded-full bg-(--color-primary) origin-center animate-[spinner-wave_0.9s_ease-in-out_infinite_0.48s]"></span>
                        </span>
                    </div>
                    <div class="text-center">
                        <p class="text-(--color-light) text-lg font-semibold">
                            ${message}
                        </p>
                        <p class="text-gray-400 text-sm mt-1">
                            Please wait a moment...
                        </p>
                    </div>
                </div>
            `,
            baseZ: 999999,
            css: {
                border: "none",
                backgroundColor: "transparent",
                cursor: "wait",
                padding: "0",
            },
            overlayCSS: {
                backgroundColor: "rgba(0,0,0,0.90)",
                cursor: "wait",
            },
        });
    },

    stop() {
        this._count = Math.max(0, this._count - 1);
        if (this._count > 0) return;

        $.unblockUI();
    },

    forceStop() {
        this._count = 0;
        $.unblockUI();
    },
};

export default Loading;
