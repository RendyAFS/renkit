export default function initFormEnterSubmit() {
    document.addEventListener(
        "keydown",
        (e) => {
            if (e.key !== "Enter") return;

            const target = e.target;

            // 1. Skip TEXTAREA (allow new lines)
            if (target && target.tagName === "TEXTAREA") {
                e.stopImmediatePropagation();
                return;
            }

            // 2. Skip BUTTON or A elements (except submit buttons)
            if (target && (target.tagName === "BUTTON" || target.tagName === "A")) {
                if (
                    target.type !== "submit" &&
                    !target.hasAttribute("data-button-loading")
                ) {
                    return;
                }
            }

            // 3. Skip if focus is inside an open HSSelect dropdown menu
            if (
                target &&
                (target.closest(".hs-select-dropdown") ||
                    target.closest("[data-hs-select-dropdown]"))
            ) {
                return;
            }

            // 4. Skip elements marked with data-no-enter-submit
            if (target && target.closest("[data-no-enter-submit]")) return;

            // 5. PRIORITAS A: Jika ada MODAL yang sedang terbuka, operasikan MODAL!
            const openModal = document.querySelector(
                ".hs-overlay:not(.hidden), [role='dialog']:not(.hidden)",
            );

            if (openModal) {
                // Cari tombol simpan/submit di dalam modal
                const modalSaveBtn = openModal.querySelector(
                    'button[type="submit"], button[id*="save"], button[id*="btn-save"], .btn-save',
                );

                if (modalSaveBtn) {
                    if (modalSaveBtn.disabled) return;
                    e.preventDefault();
                    modalSaveBtn.click();
                    return;
                }

                // Cari form di dalam modal
                const modalForm = openModal.querySelector("form");
                if (modalForm) {
                    const submitBtn = modalForm.querySelector(
                        'button[type="submit"], input[type="submit"]',
                    );
                    if (submitBtn) {
                        if (submitBtn.disabled) return;
                        e.preventDefault();
                        submitBtn.click();
                        return;
                    }
                }
                return;
            }

            // 6. PRIORITAS B: Jika TIDAK ada modal terbuka, cari form tempat target atau form utama halaman!
            let form = target ? target.closest("form") : null;

            if (!form) {
                form = document.querySelector(
                    "main form, form[data-mode], form[id$='-form']",
                );
            }

            if (!form) return;

            // 7. Temukan tombol submit utama dari form halaman
            const submitBtn = form.querySelector(
                'button[type="submit"], input[type="submit"]',
            );

            if (submitBtn && submitBtn.disabled) return;

            e.preventDefault();

            if (typeof form.requestSubmit === "function") {
                try {
                    form.requestSubmit(submitBtn || undefined);
                } catch (err) {
                    if (submitBtn) submitBtn.click();
                }
            } else if (submitBtn) {
                submitBtn.click();
            }
        },
        true,
    );
}
