import Alpine from "alpinejs";

function customAlert() {
    return {
        // Toast State
        toasts: [],
        toastCounter: 0,

        // Alert State
        alert: {
            show: false,
            title: "",
            message: "",
            type: "info",
            confirmText: "OK",
        },

        // Confirm State
        confirm: {
            show: false,
            title: "",
            message: "",
            confirmText: "Confirm",
            cancelText: "Cancel",
            onConfirm: null,
            onCancel: null,
        },

        // Icons
        icons: {
            success: `<i data-lucide="circle-check-big" class="text-(--color-green) size-6"></i>`,
            info: `<i data-lucide="info" class="text-(--color-blue) size-6"></i>`,
            warning: `<i data-lucide="triangle-alert" class="text-(--color-yellow) size-6"></i>`,
            error: `<i data-lucide="circle-x" class="text-(--color-red) size-6"></i>`,
        },

        // Default Titles
        defaultTitles: {
            success: window.langCustomAlert?.success ?? "Success",
            info: window.langCustomAlert?.info ?? "Information",
            warning: window.langCustomAlert?.warning ?? "Warning",
            error: window.langCustomAlert?.error ?? "Error",
        },

        // ============ TOAST METHODS ============
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
                this.startToastTimer(toast);

                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        },

        startToastTimer(toast) {
            toast.timer = setTimeout(() => {
                this.removeToast(toast.id);
            }, toast.remaining);
        },

        pauseToast(id) {
            const toast = this.toasts.find((t) => t.id === id);
            if (!toast || !toast.timer) return;

            clearTimeout(toast.timer);
            toast.timer = null;
            toast.remaining -= Date.now() - toast.start;
        },

        resumeToast(id) {
            const toast = this.toasts.find((t) => t.id === id);
            if (!toast || toast.timer || toast.remaining <= 0) return;

            toast.start = Date.now();
            this.startToastTimer(toast);
        },

        removeToast(id) {
            const toast = this.toasts.find((t) => t.id === id);
            if (!toast) return;

            if (toast.timer) clearTimeout(toast.timer);

            toast.show = false;

            setTimeout(() => {
                this.toasts = this.toasts.filter((t) => t.id !== id);
                if (toast.onClose) toast.onClose();
            }, 250);
        },

        // ============ ALERT METHODS ============
        showAlert(
            title,
            message,
            type = "info",
            confirmText = window.langCustomAlert?.ok ?? "OK",
        ) {
            this.alert = {
                show: true,
                title: title || this.defaultTitles[type],
                message,
                type,
                confirmText,
            };

            this.$nextTick(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });

            document.body.style.overflow = "hidden";
        },

        closeAlert() {
            this.alert.show = false;
            document.body.style.overflow = "";
        },

        // ============ CONFIRM METHODS ============
        showConfirm(
            title,
            message,
            confirmText = window.langCustomAlert?.confirm ?? "Confirm",
            cancelText = window.langCustomAlert?.cancel ?? "Cancel",
            onConfirm = null,
            onCancel = null,
        ) {
            this.confirm = {
                show: true,
                title:
                    title ||
                    (window.langCustomAlert?.confirmation ?? "Confirmation"),
                message,
                confirmText,
                cancelText,
                onConfirm,
                onCancel,
            };

            this.$nextTick(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });

            document.body.style.overflow = "hidden";
        },

        closeConfirm(confirmed) {
            this.confirm.show = false;
            document.body.style.overflow = "";

            if (confirmed && this.confirm.onConfirm) {
                this.confirm.onConfirm();
            } else if (!confirmed && this.confirm.onCancel) {
                this.confirm.onCancel();
            }

            // Reset callbacks
            this.confirm.onConfirm = null;
            this.confirm.onCancel = null;
        },
    };
}

// ============ GLOBAL TOAST API (Tetap sama) ============
window.Toast = {
    success(title = null, message, timeout = 4000) {
        document.dispatchEvent(
            new CustomEvent("toast", {
                detail: { type: "success", title, message, timeout },
            }),
        );
    },
    error(title = null, message, timeout = 4000) {
        document.dispatchEvent(
            new CustomEvent("toast", {
                detail: { type: "error", title, message, timeout },
            }),
        );
    },
    info(title = null, message, timeout = 4000) {
        document.dispatchEvent(
            new CustomEvent("toast", {
                detail: { type: "info", title, message, timeout },
            }),
        );
    },
    warning(title = null, message, timeout = 4000) {
        document.dispatchEvent(
            new CustomEvent("toast", {
                detail: { type: "warning", title, message, timeout },
            }),
        );
    },
};

// ============ GLOBAL ALERT API ============
window.Alert = {
    success(message, title = null, confirmText = "OK") {
        document.dispatchEvent(
            new CustomEvent("alert", {
                detail: { type: "success", title, message, confirmText },
            }),
        );
    },
    error(message, title = null, confirmText = "OK") {
        document.dispatchEvent(
            new CustomEvent("alert", {
                detail: { type: "error", title, message, confirmText },
            }),
        );
    },
    info(message, title = null, confirmText = "OK") {
        document.dispatchEvent(
            new CustomEvent("alert", {
                detail: { type: "info", title, message, confirmText },
            }),
        );
    },
    warning(message, title = null, confirmText = "OK") {
        document.dispatchEvent(
            new CustomEvent("alert", {
                detail: { type: "warning", title, message, confirmText },
            }),
        );
    },
};

// ============ GLOBAL CONFIRM API ============
window.Confirm = {
    show(
        message,
        title = window.langCustomAlert?.confirmation ?? "Confirmation",
        confirmText = window.langCustomAlert?.confirm ?? "Confirm",
        cancelText = window.langCustomAlert?.cancel ?? "Cancel",
    ) {
        return new Promise((resolve) => {
            document.dispatchEvent(
                new CustomEvent("confirm", {
                    detail: {
                        title,
                        message,
                        confirmText,
                        cancelText,
                        onConfirm: () => resolve(true),
                        onCancel: () => resolve(false),
                    },
                }),
            );
        });
    },

    delete(
        message = window.langCustomAlert?.deleteMessage ??
            "Are you sure you want to delete this item? This action cannot be undone.",
    ) {
        return this.show(
            message,
            window.langCustomAlert?.deleteConfirmation ?? "Delete Confirmation",
            window.langCustomAlert?.delete ?? "Delete",
            window.langCustomAlert?.cancel ?? "Cancel",
        );
    },
};

// ============ FLASH TOAST (Tetap sama) ============
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
