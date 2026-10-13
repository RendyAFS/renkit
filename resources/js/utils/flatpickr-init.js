import flatpickr from "flatpickr";
import { Indonesian } from "flatpickr/dist/l10n/id.js";

flatpickr.localize(Indonesian);

const instances = new Map();

const mondayOf = (date) => {
    const d = new Date(date);
    const day = d.getDay();
    const diff = day === 0 ? -6 : 1 - day;
    d.setDate(d.getDate() + diff);
    return d;
};

const sundayOf = (date) => {
    const monday = mondayOf(date);
    monday.setDate(monday.getDate() + 6);
    return monday;
};

const buildOptions = (el) => {
    const config = JSON.parse(el.dataset.flatpickr || "{}");
    const weekRange = !!config.weekRange;
    delete config.weekRange;

    return {
        ...config,
        onOpen(selectedDates, dateStr, instance) {
            document.addEventListener(
                "mousedown",
                instance._outsideClickHandler,
                true,
            );
        },
        onClose(selectedDates, dateStr, instance) {
            document.removeEventListener(
                "mousedown",
                instance._outsideClickHandler,
                true,
            );
        },
        onChange(selectedDates, dateStr, instance) {
            if (config.mode === "range") {
                if (weekRange && selectedDates.length === 1) {
                    const monday = mondayOf(selectedDates[0]);
                    const sunday = sundayOf(selectedDates[0]);
                    instance.setDate([monday, sunday], true);
                    return;
                }

                const [start, end] = selectedDates;
                const startInput = document.getElementById(`${el.id}_start`);
                const endInput = document.getElementById(`${el.id}_end`);

                if (startInput)
                    startInput.value = start
                        ? instance.formatDate(start, config.dateFormat)
                        : "";
                if (endInput)
                    endInput.value = end
                        ? instance.formatDate(end, config.dateFormat)
                        : "";

                el.dispatchEvent(
                    new CustomEvent("flatpickr:range-change", {
                        detail: { start, end },
                        bubbles: true,
                    }),
                );
            } else {
                const valueInput = document.getElementById(`${el.id}_value`);
                if (valueInput) valueInput.value = dateStr;

                el.dispatchEvent(
                    new CustomEvent("flatpickr:change", {
                        detail: { date: selectedDates[0], dateStr },
                        bubbles: true,
                    }),
                );
            }
        },
    };
};

export const initFlatpickrAll = (root = document) => {
    root.querySelectorAll("[data-flatpickr]").forEach((el) => {
        if (instances.has(el.id)) return;

        const instance = flatpickr(el, buildOptions(el));

        instance._outsideClickHandler = (e) => {
            const isCalendarClick = instance.calendarContainer?.contains(
                e.target,
            );
            const isInputClick =
                el.contains(e.target) || instance.altInput?.contains(e.target);

            if (!isCalendarClick && !isInputClick) {
                instance.close();
            }
        };

        instances.set(el.id, instance);
    });

    root.querySelectorAll("[data-flatpickr-clear]").forEach((btn) => {
        btn.addEventListener("click", () => {
            instances.get(btn.dataset.flatpickrClear)?.clear();
        });
    });
};

export const getFlatpickrInstance = (id) => instances.get(id);

document.addEventListener("DOMContentLoaded", () => initFlatpickrAll());
