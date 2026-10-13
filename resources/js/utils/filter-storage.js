const buildKey = () => `filters:${window.location.pathname}`;

const getUrlParams = () => new URLSearchParams(window.location.search);

const hasValues = (params) => [...params.entries()].some(([, v]) => v !== "");

const loadFilterParams = () => {
    const params = getUrlParams();
    if (hasValues(params)) return params;

    const stored = sessionStorage.getItem(buildKey());
    return stored ? new URLSearchParams(stored) : new URLSearchParams();
};

const saveFilterParams = (params) => {
    sessionStorage.setItem(buildKey(), params.toString());
    window.history.replaceState(
        {},
        "",
        `${window.location.pathname}?${params.toString()}`,
    );
};

export default { loadFilterParams, saveFilterParams };
