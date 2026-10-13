import { initLucide } from "./lucide";

document.addEventListener("hsSelect.afterInit", (e) => {
    initLucide(e.target);
});

window.setSelectValue = (selector, value) => {
    const select = document.querySelector(selector);
    if (!select) return;

    select.value = value;
    select.dispatchEvent(new Event("change", { bubbles: true }));

    if (window.HSSelect) {
        const instance = HSSelect.getInstance(select);
        instance?.setValue(value);
    }
};

document.addEventListener("DOMContentLoaded", () => {
    const toggleClearButton = (select) => {
        const btn = document.querySelector(
            `[data-clear-select="${select.id}"]`,
        );
        if (!btn) return;

        if (select.value && select.value !== "") {
            btn.style.display = "inline-flex";
        } else {
            btn.style.display = "none";
        }
    };

    document.querySelectorAll("select[data-hs-select]").forEach((select) => {
        toggleClearButton(select);

        select.addEventListener("change", () => {
            toggleClearButton(select);
        });
    });

    document.addEventListener("click", (e) => {
        const btn = e.target.closest("[data-clear-select]");
        if (!btn) return;

        const selectId = btn.getAttribute("data-clear-select");
        const select = document.getElementById(selectId);
        if (!select) return;

        select.value = "";

        select.dispatchEvent(new Event("change", { bubbles: true }));

        if (window.HSSelect) {
            const instance = HSSelect.getInstance(select);
            instance?.setValue("");
        }

        btn.style.display = "none";
    });
});
