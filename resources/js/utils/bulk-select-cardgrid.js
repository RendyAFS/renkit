const DEFAULT_LONG_PRESS_MS = 500;
const DEFAULT_MOVE_THRESHOLD = 10;

export default function initBulkSelectCardgrid({
    gridId,
    barId,
    countId,
    checkboxSelector,
    cardSelector = ".cg-card-wrapper",
    countText,
    longPressMs = DEFAULT_LONG_PRESS_MS,
    moveThreshold = DEFAULT_MOVE_THRESHOLD,
    onSelectionChange,
}) {
    const gridEl = document.getElementById(gridId);
    const barEl = document.getElementById(barId);
    const countEl = document.getElementById(countId);

    if (!gridEl || !barEl || !countEl) return null;

    let selectMode = false;
    let selectedIds = new Set();
    let longPressTimer = null;
    let longPressFired = false;
    let pressStartX = 0;
    let pressStartY = 0;

    const clearSelectionUI = () => {
        gridEl
            .querySelectorAll(checkboxSelector)
            .forEach((cb) => (cb.checked = false));
        gridEl
            .querySelectorAll(`${cardSelector}.selected`)
            .forEach((el) => el.classList.remove("selected"));
    };

    const updateBar = () => {
        const count = selectedIds.size;

        countEl.textContent = countText(count);
        barEl.classList.toggle("hidden", count === 0);

        if (count === 0 && selectMode) {
            selectMode = false;
            gridEl.classList.remove("select-mode");
        }

        onSelectionChange?.(Array.from(selectedIds), count);
    };

    const toggleSelection = (wrapper, checked, id) => {
        if (checked) {
            selectedIds.add(id);
            wrapper.classList.add("selected");
        } else {
            selectedIds.delete(id);
            wrapper.classList.remove("selected");
        }
        updateBar();
    };

    const cancelLongPress = () => {
        clearTimeout(longPressTimer);
        longPressTimer = null;
    };

    const startLongPress = (wrapper) => {
        longPressFired = false;
        longPressTimer = setTimeout(() => {
            longPressFired = true;
            selectMode = true;
            gridEl.classList.add("select-mode");

            const checkbox = wrapper.querySelector(checkboxSelector);
            if (checkbox && !checkbox.disabled) {
                checkbox.checked = true;
                toggleSelection(wrapper, true, checkbox.dataset.id);
            }

            try {
                navigator.vibrate?.(20);
            } catch (e) {}
        }, longPressMs);
    };

    gridEl.addEventListener("pointerdown", function (e) {
        if (e.target.closest("button, a, [data-no-card-click]")) return;

        const wrapper = e.target.closest(cardSelector);
        if (!wrapper) return;

        pressStartX = e.clientX;
        pressStartY = e.clientY;

        startLongPress(wrapper);
    });

    gridEl.addEventListener("pointermove", function (e) {
        if (!longPressTimer) return;

        const dx = Math.abs(e.clientX - pressStartX);
        const dy = Math.abs(e.clientY - pressStartY);

        if (dx > moveThreshold || dy > moveThreshold) {
            cancelLongPress();
        }
    });

    gridEl.addEventListener("pointerup", cancelLongPress);
    gridEl.addEventListener("pointerleave", cancelLongPress);
    gridEl.addEventListener("pointercancel", cancelLongPress);

    gridEl.addEventListener(
        "click",
        function (e) {
            if (longPressFired) {
                longPressFired = false;
                e.preventDefault();
                e.stopImmediatePropagation();
                return;
            }

            if (!selectMode) return;

            const wrapper = e.target.closest(cardSelector);
            if (!wrapper) return;

            e.preventDefault();
            e.stopImmediatePropagation();

            const checkbox = wrapper.querySelector(checkboxSelector);
            if (!checkbox || checkbox.disabled) return;

            checkbox.checked = !checkbox.checked;
            toggleSelection(wrapper, checkbox.checked, checkbox.dataset.id);
        },
        true,
    );

    new MutationObserver(() => {
        if (selectMode) {
            selectedIds.clear();
            updateBar();
        }
    }).observe(gridEl, { childList: true });

    return {
        getSelectedIds: () => Array.from(selectedIds),
        reset: () => {
            selectedIds.clear();
            clearSelectionUI();
            updateBar();
        },
    };
}
