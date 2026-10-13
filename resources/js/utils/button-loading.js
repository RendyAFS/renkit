function setLoading(button, isLoading) {
    if (!button) return;

    const spinner = button.querySelector("[data-spinner]");
    const text = button.querySelector("[data-text]");
    const icon = button.querySelector("[data-icon]");
    const loadingText = button.dataset.loadingText;
    const originalText = button.dataset.originalText;

    if (isLoading) {
        // simpan text asli sekali
        if (!originalText && text) {
            button.dataset.originalText = text.textContent;
        }

        button.disabled = true;
        spinner?.classList.remove("hidden");
        icon?.classList.add("hidden");

        if (text && loadingText) {
            text.textContent = loadingText;
        }
    } else {
        button.disabled = false;
        spinner?.classList.add("hidden");
        icon?.classList.remove("hidden");

        if (text && originalText) {
            text.textContent = originalText;
        }
    }
}

function startLoading(button) {
    setLoading(button, true);
}

function stopLoading(button) {
    setLoading(button, false);
}

export { startLoading, stopLoading };
