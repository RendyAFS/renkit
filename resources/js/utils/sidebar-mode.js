document.addEventListener("DOMContentLoaded", () => {
    const STORAGE_KEY = "sidebar:mode";
    const DESKTOP_QUERY = "(min-width: 1024px)";

    document.documentElement.classList.remove("sidebar-floating-init");

    const sidebarWrapper = document.querySelector("#hs-sidebar-content-push");
    const contentArea = document.querySelector("#app-content-area");
    const floatingTrigger = document.querySelector("#sidebar-floating-trigger");
    const modeButton = document.querySelector("#sidebar-mode-switch");
    const backButton = document.querySelector("#sidebar-back-to-mode");
    const navbarLogo = document.querySelector("#navbar-logo");

    if (!sidebarWrapper) return;

    const getStoredMode = () => localStorage.getItem(STORAGE_KEY) || "sidebar";
    const setStoredMode = (mode) => localStorage.setItem(STORAGE_KEY, mode);

    const applyMode = () => {
        const desktop = window.matchMedia(DESKTOP_QUERY).matches;
        const storedMode = getStoredMode();
        const floatingActive = !desktop || storedMode === "floating";

        sidebarWrapper.style.display = floatingActive ? "none" : "";

        if (contentArea) {
            contentArea.style.marginInlineStart = floatingActive ? "0px" : "";
        }

        if (floatingTrigger) {
            floatingTrigger.style.display = floatingActive ? "flex" : "none";
        }

        if (backButton) {
            backButton.style.display =
                desktop && floatingActive ? "flex" : "none";
        }

        if (navbarLogo) {
            navbarLogo.style.display = floatingActive ? "flex" : "none";
        }
    };

    modeButton?.addEventListener("click", () => {
        setStoredMode("floating");
        applyMode();
    });

    backButton?.addEventListener("click", () => {
        setStoredMode("sidebar");
        applyMode();
        window.HSOverlay?.close(
            document.querySelector("#sidebar-floating-modal"),
        );
    });

    let resizeTimeout;
    window.addEventListener("resize", () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(applyMode, 150);
    });

    applyMode();
});
