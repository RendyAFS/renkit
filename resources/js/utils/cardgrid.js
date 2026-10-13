import { initLucide } from "@/utils/lucide";

export default function initCardgrid({
    containerId,
    filterSelector = null,
    ajax,
    renderCard,
    pageLength = 12,
    cardClickRoute = null,
}) {
    const container = document.querySelector(containerId);
    if (!container) return null;

    const gridId = containerId.replace("#", "");

    let state = {
        page: 1,
        perPage: pageLength,
        search: "",
        filter: null,
        total: 0,
        lastPage: 1,
    };

    let isLoading = false;
    let searchTimeout = null;

    const toggleControlsDisabled = (disabled) => {
        const controlsWrapper = document.querySelector(
            `[data-cg-controls="${gridId}"]`,
        );
        if (controlsWrapper) {
            controlsWrapper
                .querySelectorAll("input, select, button, .btn-group-item")
                .forEach((el) => {
                    if (el.id === "cg-search") return;
                    el.disabled = disabled;
                });

            controlsWrapper.querySelectorAll(".hs-select").forEach((el) => {
                el.classList.toggle("pointer-events-none", disabled);
                el.classList.toggle("opacity-50", disabled);
            });
        }

        // Fallback for individual elements
        const lengthEl = document.getElementById("cg-length");
        if (lengthEl) {
            lengthEl.disabled = disabled;
            const hsWrapper = lengthEl.closest(".hs-select");
            if (hsWrapper) {
                hsWrapper.classList.toggle("pointer-events-none", disabled);
                hsWrapper.classList.toggle("opacity-50", disabled);
            }
        }

        if (filterSelector) {
            document.querySelectorAll(filterSelector).forEach((filterDom) => {
                filterDom.disabled = disabled;
                const hsWrapper = filterDom.closest(".hs-select");
                if (hsWrapper) {
                    hsWrapper.classList.toggle("pointer-events-none", disabled);
                    hsWrapper.classList.toggle("opacity-50", disabled);
                }
            });
        }

        const pagEl = document.getElementById("cg-pagination");
        if (pagEl) {
            pagEl.classList.toggle("pointer-events-none", disabled);
            pagEl.classList.toggle("opacity-50", disabled);
            pagEl.querySelectorAll("button").forEach((btn) => {
                btn.disabled = disabled;
            });
        }

        // Filter di luar component — tandai wrapper-nya dengan: data-cg-page-filters="<gridId>"
        document
            .querySelectorAll(`[data-cg-page-filters="${gridId}"]`)
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

    const fetchData = async () => {
        setLoading(true);

        const params = new URLSearchParams({
            page: state.page,
            per_page: state.perPage,
            search: state.search,
        });

        if (state.filter) params.append("filter", state.filter);

        if (ajax.data) {
            const extra = ajax.data();
            Object.entries(extra).forEach(([k, v]) => params.append(k, v));
        }

        try {
            const res = await fetch(`${ajax.url}?${params.toString()}`, {
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });
            const json = await res.json();

            state.total = json.meta?.total ?? json.total ?? 0;
            state.lastPage = json.meta?.last_page ?? json.last_page ?? 1;

            render(json.data ?? []);
            renderPagination();
            renderInfo();
        } catch (e) {
            console.error("CardGrid fetch error:", e);
        } finally {
            setLoading(false);
        }
    };

    // Card Click Delegated Event Listener (attached once)
    if (cardClickRoute) {
        container.addEventListener("click", function (e) {
            const cardEl = e.target.closest(".cg-card-wrapper");
            if (!cardEl) return;
            if (
                e.target.closest(
                    "button, a, .btn-delete, .btn-restore, .btn-force-delete, [data-no-card-click]",
                )
            ) {
                return;
            }

            const id = cardEl.dataset.id;
            if (!id) return;

            window.location.href = cardClickRoute({ id });
        });
    }

    const render = (items) => {
        container.innerHTML = "";

        const emptyEl = document.getElementById(`${gridId}-empty`);
        const skeletonEl = document.getElementById(`${gridId}-skeleton`);

        skeletonEl?.classList.add("hidden");

        if (!items.length) {
            emptyEl?.classList.remove("hidden");
            container.classList.add("hidden");
            return;
        }

        emptyEl?.classList.add("hidden");
        container.classList.remove("hidden");
        container.innerHTML = items
            .map((item) => {
                return `
                <div class="cg-card-wrapper" data-id="${item.id}">
                    ${renderCard(item)}
                </div>
            `;
            })
            .join("");

        initLucide();
        if (window.HSStaticMethods) window.HSStaticMethods.autoInit();
    };

    const setLoading = (loading) => {
        isLoading = loading;
        const loadingEl = document.getElementById(`${gridId}-loading`);
        const skeletonEl = document.getElementById(`${gridId}-skeleton`);
        const emptyEl = document.getElementById(`${gridId}-empty`);

        if (loading) {
            emptyEl?.classList.add("hidden");
            const hasExistingCards =
                container.querySelectorAll(".cg-card-wrapper").length > 0 &&
                !container.classList.contains("hidden");

            if (!hasExistingCards && skeletonEl) {
                skeletonEl.classList.remove("hidden");
                container.classList.add("hidden");
                loadingEl?.classList.add("hidden");
            } else {
                skeletonEl?.classList.add("hidden");
                loadingEl?.classList.remove("hidden");

                container.classList.add(
                    "pointer-events-none",
                    "opacity-40",
                    "scale-[0.99]",
                );
            }
        } else {
            skeletonEl?.classList.add("hidden");
            loadingEl?.classList.add("hidden");

            container.classList.remove(
                "pointer-events-none",
                "opacity-40",
                "scale-[0.99]",
            );
        }

        toggleControlsDisabled(loading);
    };

    const renderInfo = () => {
        const from = (state.page - 1) * state.perPage + 1;
        const to = Math.min(state.page * state.perPage, state.total);
        const infoEl = document.getElementById("cg-info");
        const lang = window.cardgridLang?.[gridId] ?? {};

        if (infoEl) {
            infoEl.textContent = state.total
                ? (lang.showing ?? `Showing ${from} – ${to} of ${state.total}`)
                      .replace(":from", from)
                      .replace(":to", to)
                      .replace(":total", state.total)
                : (lang.noResults ?? "No results");
        }
    };

    const renderPagination = () => {
        const paginationEl = document.getElementById("cg-pagination");
        if (!paginationEl) return;
        paginationEl.innerHTML = "";

        const current = state.page - 1;
        const total = state.lastPage;
        const last = total - 1;

        const btnTpl = document.getElementById("cg-pagination-btn-template");
        const ellipsisTpl = document.getElementById(
            "cg-pagination-ellipsis-template",
        );
        const prevTpl = document.getElementById("cg-pagination-prev-template");
        const nextTpl = document.getElementById("cg-pagination-next-template");

        const createBtn = (page, label = null, isCurrent = false) => {
            const btn = btnTpl.content.cloneNode(true).querySelector("button");
            btn.dataset.page = page + 1;
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

        const prevBtn = prevTpl.content.cloneNode(true).querySelector("button");
        prevBtn.dataset.page = state.page - 1;
        if (current === 0) {
            prevBtn.classList.add("opacity-50", "cursor-not-allowed");
            prevBtn.disabled = true;
        } else prevBtn.classList.add("hover:bg-(--color-light-gray)");
        paginationEl.append(prevBtn);

        paginationEl.append(createBtn(0, null, current === 0));

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

        if (start > 1) paginationEl.append(ellipsisTpl.content.cloneNode(true));
        for (let i = start; i <= end; i++) {
            if (i > 0 && i < last)
                paginationEl.append(createBtn(i, null, i === current));
        }
        if (end < last - 1)
            paginationEl.append(ellipsisTpl.content.cloneNode(true));
        if (last > 0)
            paginationEl.append(createBtn(last, total, current === last));

        const nextBtn = nextTpl.content.cloneNode(true).querySelector("button");
        nextBtn.dataset.page = state.page + 1;
        if (current === last) {
            nextBtn.classList.add("opacity-50", "cursor-not-allowed");
            nextBtn.disabled = true;
        } else nextBtn.classList.add("hover:bg-(--color-light-gray)");
        paginationEl.append(nextBtn);

        initLucide();
    };

    const searchInput = document.getElementById("cg-search");
    const clearBtn = document.getElementById("cg-search-clear");

    searchInput?.addEventListener("input", function () {
        if (isLoading) return;

        const value = this.value;

        // toggle clear button
        if (value.length > 0) {
            clearBtn?.classList.remove("hidden");
            clearBtn?.classList.add("flex");
        } else {
            clearBtn?.classList.remove("flex");
            clearBtn?.classList.add("hidden");
        }

        clearTimeout(searchTimeout);

        searchTimeout = setTimeout(() => {
            state.search = value;
            state.page = 1;
            fetchData();
        }, 500);
    });

    // clear search
    clearBtn?.addEventListener("click", () => {
        if (isLoading) return;

        searchInput.value = "";

        clearBtn.classList.remove("flex");
        clearBtn.classList.add("hidden");

        state.search = "";
        state.page = 1;

        fetchData();

        searchInput.focus();
    });

    document
        .getElementById("cg-length")
        ?.addEventListener("change", function () {
            if (isLoading) return;
            state.perPage = parseInt(this.value);
            state.page = 1;
            fetchData();
        });

    if (filterSelector) {
        document.addEventListener("change", (e) => {
            if (isLoading) return;
            if (e.target.matches(filterSelector)) {
                state.filter = e.target.value;
                state.page = 1;
                fetchData();
            }
        });
    }

    document.addEventListener("click", (e) => {
        if (isLoading) return;
        const btn = e.target.closest("#cg-pagination button[data-page]");
        if (!btn) return;
        const page = parseInt(btn.dataset.page);
        if (!isNaN(page) && page >= 1 && page <= state.lastPage) {
            state.page = page;
            fetchData();
        }
    });

    const reload = () => fetchData();

    fetchData();

    return { reload };
}
