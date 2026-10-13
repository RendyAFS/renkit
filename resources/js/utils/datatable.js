import { initLucide } from "@/utils/lucide";

if (window.$ && window.$.fn && window.$.fn.dataTable) {
    window.$.fn.dataTable.ext.errMode = "none";
}

export default function initDatatable({
    table,
    ajax,
    columns,
    pageLength,
    order = [],
    filterSelector = null,
    rowClickRoute = null,
    onRowClick = null,
}) {
    if (window.$ && window.$.fn && window.$.fn.dataTable) {
        window.$.fn.dataTable.ext.errMode = "none";
    }

    if (!document.querySelector(table)) return;

    const tableId = table.replace("#", "");

    if (!pageLength) {
        pageLength =
            parseInt(document.getElementById(`dt-length-${tableId}`)?.value) ||
            10;
    }

    const filterId = filterSelector
        ? filterSelector.startsWith("#") || filterSelector.startsWith(".")
            ? filterSelector
            : `#${filterSelector}`
        : null;
    const filterEl = filterId ? $(filterId) : null;

    const ajaxOptions = typeof ajax === "string" ? { url: ajax } : { ...ajax };
    const originalData = ajaxOptions.data;

    ajaxOptions.data = function (d) {
        if (typeof originalData === "function") {
            originalData(d);
        } else if (originalData) {
            Object.assign(d, originalData);
        }

        if (filterEl && filterEl.length) {
            d.filter = filterEl.val();
        }

        return d;
    };

    let isProcessing = false;

    const toggleControlsDisabled = (disabled) => {
        const controlsWrapper = document.querySelector(
            `[data-dt-controls="${tableId}"]`,
        );
        if (controlsWrapper) {
            controlsWrapper
                .querySelectorAll("input, select, button, .btn-group-item")
                .forEach((el) => {
                    if (el.id === `dt-search-${tableId}`) return;
                    el.disabled = disabled;
                });

            controlsWrapper.querySelectorAll(".hs-select").forEach((el) => {
                el.classList.toggle("pointer-events-none", disabled);
                el.classList.toggle("opacity-50", disabled);
            });
        }

        // Fallback for individual elements
        const lengthEl = document.getElementById(`dt-length-${tableId}`);
        if (lengthEl) {
            lengthEl.disabled = disabled;
            const hsWrapper = lengthEl.closest(".hs-select");
            if (hsWrapper) {
                hsWrapper.classList.toggle("pointer-events-none", disabled);
                hsWrapper.classList.toggle("opacity-50", disabled);
            }
        }

        if (filterId) {
            const filterDom = document.querySelector(filterId);
            if (filterDom) {
                filterDom.disabled = disabled;
                const hsWrapper = filterDom.closest(".hs-select");
                if (hsWrapper) {
                    hsWrapper.classList.toggle("pointer-events-none", disabled);
                    hsWrapper.classList.toggle("opacity-50", disabled);
                }
            }
        }

        const pagEl = document.getElementById(`dt-pagination-${tableId}`);
        if (pagEl) {
            pagEl.classList.toggle("pointer-events-none", disabled);
            pagEl.classList.toggle("opacity-50", disabled);
            pagEl.querySelectorAll("button").forEach((btn) => {
                btn.disabled = disabled;
            });
        }

        // Filter di luar component — tandai wrapper-nya dengan: data-dt-page-filters="<tableId>"
        document
            .querySelectorAll(`[data-dt-page-filters="${tableId}"]`)
            .forEach((wrapper) => {
                wrapper.classList.toggle("pointer-events-none", disabled);
                wrapper.classList.toggle("opacity-50", disabled);
                wrapper
                    .querySelectorAll("input, select, button, textarea")
                    .forEach((el) => {
                        el.disabled = disabled;
                    });
                wrapper
                    .querySelectorAll(".hs-select, .flatpickr-input")
                    .forEach((el) => {
                        el.classList.toggle("pointer-events-none", disabled);
                    });
            });
    };

    // Disable semua kontrol sampai data pertama berhasil dimuat
    toggleControlsDisabled(true);

    let firstDraw = true;

    const datatable = $(table).DataTable({
        dom: "t",
        paging: true,
        pageLength,
        lengthChange: false,
        info: false,
        processing: false,

        serverSide: false,
        ajax: ajaxOptions,
        columns,
        order,
        drawCallback() {
            initLucide();
            const skeletonEl = document.getElementById(
                `dt-skeleton-${tableId}`,
            );
            const wrapperEl = document.getElementById(`dt-wrapper-${tableId}`);

            // Hanya saat draw pertama: sembunyikan skeleton & aktifkan kembali semua kontrol
            if (firstDraw) {
                firstDraw = false;
                if (skeletonEl) skeletonEl.classList.add("hidden");
                if (wrapperEl) wrapperEl.classList.remove("hidden");
                toggleControlsDisabled(false);
            }

            const rows = $(`${table} tbody tr`);
            rows.addClass(
                "hover:!bg-(--color-light-gray) dark:hover:!bg-(--color-dark-slate) cursor-pointer transition",
            );

            if (window.HSStaticMethods) window.HSStaticMethods.autoInit();
        },
    });

    // Delegated Row Click Event Listener (attached once)
    if (rowClickRoute || onRowClick) {
        $(table)
            .off("click.rowClick", "tbody tr")
            .on("click.rowClick", "tbody tr", function (e) {
                if (
                    $(e.target).closest(
                        "button, a, .hs-dropdown, .hs-dropdown-menu, .toggle-active, label, input, [data-no-row-click]",
                    ).length
                ) {
                    return;
                }

                const rowData = datatable.row(this).data();
                if (!rowData) return;

                if (onRowClick) {
                    onRowClick(rowData, this);
                    return;
                }

                if (rowClickRoute) {
                    window.location.href = rowClickRoute(rowData);
                }
            });
    }

    // Skeleton & Processing Overlay Event Handler
    const skeletonEl = document.getElementById(`dt-skeleton-${tableId}`);
    const wrapperEl = document.getElementById(`dt-wrapper-${tableId}`);

    datatable.on("processing.dt", function (e, settings, processing) {
        isProcessing = processing;
        toggleControlsDisabled(processing);

        if (!skeletonEl || !wrapperEl) return;

        if (processing) {
            const hasRows =
                $(table).find("tbody tr").length > 0 &&
                !$(table).find("tbody tr td.dataTables_empty").length;
            if (!hasRows) {
                skeletonEl.classList.remove("hidden");
                wrapperEl.classList.add("hidden");
            }
        } else {
            skeletonEl.classList.add("hidden");
            wrapperEl.classList.remove("hidden");
        }
    });

    let searchTimeout = null;

    const searchInput = $(`#dt-search-${tableId}`);
    const clearBtn = $(`#dt-search-clear-${tableId}`);
    const lengthSelect = $(`#dt-length-${tableId}`);
    const paginationContainer = $(`#dt-pagination-${tableId}`);
    const infoContainer = $(`#dt-info-${tableId}`);

    searchInput.on("keyup input", function () {
        if (isProcessing) return;

        const value = this.value;

        if (value.length > 0) {
            clearBtn.removeClass("hidden").addClass("flex");
        } else {
            clearBtn.removeClass("flex").addClass("hidden");
        }

        clearTimeout(searchTimeout);

        searchTimeout = setTimeout(() => {
            datatable.search(value).draw();
        }, 500);
    });

    clearBtn.on("click", function () {
        if (isProcessing) return;

        searchInput.val("");

        clearBtn.removeClass("flex").addClass("hidden");

        datatable.search("").draw();

        searchInput.trigger("focus");
    });

    lengthSelect.on("change", function () {
        if (isProcessing) return;
        datatable.page.len(this.value).draw();
    });

    if (filterId) {
        $(document).on("change", filterId, function () {
            if (isProcessing) return;
            datatable.ajax.reload(null, false);
        });
    }

    paginationContainer.on("click", "button[data-page]", function () {
        if (isProcessing) return;
        datatable.page($(this).data("page")).draw("page");
    });

    const renderPagination = () => {
        const info = datatable.page.info();
        const container = paginationContainer;
        container.empty();

        const current = info.page;
        const total = info.pages;
        const last = total - 1;

        const btnTemplate = document.getElementById(
            `dt-pagination-btn-template-${tableId}`,
        );
        const ellipsisTemplate = document.getElementById(
            `dt-pagination-ellipsis-template-${tableId}`,
        );
        const prevTemplate = document.getElementById(
            `dt-pagination-prev-template-${tableId}`,
        );
        const nextTemplate = document.getElementById(
            `dt-pagination-next-template-${tableId}`,
        );

        const createBtn = (page, label = null, isCurrent = false) => {
            const btn = btnTemplate.content
                .cloneNode(true)
                .querySelector("button");
            btn.dataset.page = page;
            btn.textContent = label ?? page + 1;

            if (isCurrent) {
                btn.classList.add(
                    "bg-(--color-primary)",
                    "text-(--color-light)",
                );
                btn.setAttribute("aria-current", "page");
            } else {
                btn.classList.add(
                    "border",
                    "border-transparent",
                    "text-(--color-dark)",
                    "dark:text-(--color-light)",
                    "hover:bg-(--color-light-gray)",
                    "dark:hover:bg-(--color-dark-gray)",
                );
            }

            return btn;
        };

        const prevBtn = prevTemplate.content
            .cloneNode(true)
            .querySelector("button");
        prevBtn.dataset.page = current - 1;
        if (current === 0) {
            prevBtn.classList.add("opacity-50", "cursor-not-allowed");
            prevBtn.disabled = true;
        } else {
            prevBtn.classList.add("hover:bg-(--color-light-gray)");
        }
        container.append(prevBtn);

        container.append(createBtn(0, null, current === 0));

        let start, end;
        if (current <= 3) {
            start = 1;
            end = 4;
        } else if (current >= last - 3) {
            start = last - 4;
            end = last - 1;
        } else {
            start = current - 1;
            end = current + 1;
        }

        if (start > 1) {
            container.append(ellipsisTemplate.content.cloneNode(true));
        }

        for (let i = start; i <= end; i++) {
            if (i > 0 && i < last) {
                container.append(createBtn(i, null, i === current));
            }
        }

        if (end < last - 1) {
            container.append(ellipsisTemplate.content.cloneNode(true));
        }

        if (last > 0) {
            container.append(createBtn(last, total, current === last));
        }

        const nextBtn = nextTemplate.content
            .cloneNode(true)
            .querySelector("button");
        nextBtn.dataset.page = current + 1;
        if (current === last) {
            nextBtn.classList.add("opacity-50", "cursor-not-allowed");
            nextBtn.disabled = true;
        } else {
            nextBtn.classList.add("hover:bg-(--color-light-gray)");
        }
        container.append(nextBtn);

        const lang = window.langDatatable?.[tableId] ?? {};
        const template = lang.showing ?? "Showing :from - :to of :total";

        infoContainer.text(
            template
                .replace(":from", info.start + 1)
                .replace(":to", info.end)
                .replace(":total", info.recordsTotal),
        );

        initLucide();
    };

    datatable.on("draw", renderPagination);

    return datatable;
}
