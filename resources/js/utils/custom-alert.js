import Alpine from "alpinejs";

function customAlert() {
    return {
        toasts: [],
        toastCounter: 0,

        alert: {
            show: false,
            title: "",
            message: "",
            type: "info",
            confirmText: "OK",
        },

        confirm: {
            show: false,
            title: "",
            message: "",
            confirmText: "Konfirmasi",
            cancelText: "Batal",
            onConfirm: null,
            onCancel: null,
        },

        icons: {
            success: `<i data-lucide="circle-check-big" class="text-(--color-green) size-6"></i>`,
            info: `<i data-lucide="info" class="text-(--color-blue) size-6"></i>`,
            warning: `<i data-lucide="triangle-alert" class="text-(--color-yellow) size-6"></i>`,
            error: `<i data-lucide="circle-x" class="text-(--color-red) size-6"></i>`,
        },

        defaultTitles: {
            get success() { return window.renkitLang?.success || "Sukses"; },
            get info() { return window.renkitLang?.info || "Informasi"; },
            get warning() { return window.renkitLang?.warning || "Peringatan"; },
            get error() { return window.renkitLang?.error || "Error"; },
        },

        showToast(message, type = "info", timeout = 4000, title = null) {
            const id = ++this.toastCounter;
            const start = Date.now();

            this.toasts.push({
                id,
                title: title ?? this.defaultTitles[type],
                message,
                type,
                show: false,
                timeout,
                start,
                remaining: timeout,
                timer: null,
            });

            this.$nextTick(() => {
                const toast = this.toasts.find((t) => t.id === id);
                if (!toast) return;

                toast.show = true;
                if (window.lucide) window.lucide.createIcons();
                this.startToastTimer(toast);
            });
        },

        startToastTimer(toast) {
            toast.timer = setTimeout(() => {
                this.removeToast(toast.id);
            }, toast.remaining);
        },

        removeToast(id) {
            const index = this.toasts.findIndex((t) => t.id === id);
            if (index === -1) return;

            clearTimeout(this.toasts[index].timer);
            this.toasts[index].show = false;

            setTimeout(() => {
                this.toasts = this.toasts.filter((t) => t.id !== id);
            }, 300);
        },
    };
}

window.Toast = {
    show(title, message, type = "info", timeout = 4000) {
        document.dispatchEvent(
            new CustomEvent("toast", {
                detail: { title, message, type, timeout },
            }),
        );
    },
    success(title, message, timeout = 4000) {
        this.show(title, message, "success", timeout);
    },
    error(title, message, timeout = 4000) {
        this.show(title, message, "error", timeout);
    },
    info(title, message, timeout = 4000) {
        this.show(title, message, "info", timeout);
    },
    warning(title, message, timeout = 4000) {
        this.show(title, message, "warning", timeout);
    },
};

window.Confirm = {
    delete(message = null) {
        const msg = message || window.renkitLang?.delete_confirm || "Apakah Anda yakin ingin menghapus data ini?";
        return new Promise((resolve) => {
            if (confirm(msg)) {
                resolve(true);
            } else {
                resolve(false);
            }
        });
    },
};

window.flashToast = function (type, title = null, message, timeout = 4000) {
    sessionStorage.setItem(
        "flash_toast",
        JSON.stringify({ type, title, message, timeout }),
    );
};

document.addEventListener("DOMContentLoaded", () => {
    Object.keys(sessionStorage)
        .filter((key) => key.startsWith("flash_toast"))
        .forEach((key) => {
            const { type, title, message, timeout } = JSON.parse(
                sessionStorage.getItem(key),
            );

            setTimeout(() => {
                if (window.Toast?.[type]) {
                    window.Toast[type](title, message, timeout);
                }
            }, 350);

            sessionStorage.removeItem(key);
        });
});

Alpine.data("customAlert", customAlert);
window.Alpine = Alpine;
export default customAlert;
