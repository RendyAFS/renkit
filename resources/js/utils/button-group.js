const activeClasses = [
    "bg-(--color-primary)",
    "border-(--color-primary)",
    "text-white",
];
const inactiveClasses = [
    "bg-(--color-light)",
    "dark:bg-(--color-dark)",
    "border-(--color-gray)",
    "dark:border-(--color-slate)",
    "text-(--color-dark)",
    "dark:text-(--color-light)",
    "hover:bg-(--color-primary)/10",
];

const setActive = (btn, isActive) => {
    btn.dataset.active = isActive ? "true" : "false";

    if (isActive) {
        btn.classList.remove(...inactiveClasses);
        btn.classList.add(...activeClasses);
    } else {
        btn.classList.remove(...activeClasses);
        btn.classList.add(...inactiveClasses);
    }
};

const applyActiveState = (group, input, allValue) => {
    const values = input.value === "" ? [] : input.value.split(",");

    group.querySelectorAll(".btn-group-item").forEach((btn) => {
        const isAllBtn = btn.dataset.value === allValue;
        const isActive =
            values.length === 0 ? isAllBtn : values.includes(btn.dataset.value);
        setActive(btn, isActive);
    });
};

const handleSingleClick = (group, input, btn) => {
    group
        .querySelectorAll(".btn-group-item")
        .forEach((b) => setActive(b, false));
    setActive(btn, true);
    input.value = btn.dataset.value;
};

const handleMultipleClick = (group, input, btn, allValue) => {
    const clickedValue = btn.dataset.value;
    const allBtn = group.querySelector(
        `.btn-group-item[data-value="${allValue}"]`,
    );

    if (clickedValue === allValue) {
        group
            .querySelectorAll(".btn-group-item")
            .forEach((b) => setActive(b, b === btn));
        input.value = allValue;
        return;
    }

    setActive(btn, btn.dataset.active !== "true");
    if (allBtn) setActive(allBtn, false);

    const selected = [...group.querySelectorAll(".btn-group-item")]
        .filter(
            (b) => b.dataset.value !== allValue && b.dataset.active === "true",
        )
        .map((b) => b.dataset.value);

    if (selected.length === 0) {
        if (allBtn) setActive(allBtn, true);
        input.value = allValue;
    } else {
        input.value = selected.join(",");
    }
};

const initButtonGroup = (group) => {
    const input = document.getElementById(group.dataset.target);
    if (!input) return;

    const multiple = group.dataset.multiple === "true";
    const allValue = group.dataset.allValue ?? "";

    group.querySelectorAll(".btn-group-item").forEach((btn) => {
        btn.addEventListener("click", () => {
            if (multiple) {
                handleMultipleClick(group, input, btn, allValue);
            } else {
                handleSingleClick(group, input, btn);
            }

            input.dispatchEvent(new Event("change", { bubbles: true }));
        });
    });
};

window.setButtonGroupValue = (id, value) => {
    const input = document.getElementById(id);
    const group = document.getElementById(`${id}-group`);
    if (!input || !group) return;

    input.value = value ?? "";

    const allValue = group.dataset.allValue ?? "";
    applyActiveState(group, input, allValue);
};

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-button-group]").forEach(initButtonGroup);
});
