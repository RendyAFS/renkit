function syncThemeIcon() {
    const theme = localStorage.getItem("hs_theme") || "auto";

    document
        .querySelectorAll(".theme-icon")
        .forEach((i) => i.classList.add("hidden"));

    if (theme === "light") {
        document.querySelector(".theme-light")?.classList.remove("hidden");
    } else if (theme === "dark") {
        document.querySelector(".theme-dark")?.classList.remove("hidden");
    } else {
        document.querySelector(".theme-auto")?.classList.remove("hidden");
    }
}

syncThemeIcon();

document.addEventListener("click", (e) => {
    if (e.target.closest("[data-hs-theme-click-value]")) {
        setTimeout(syncThemeIcon, 50);
    }
});
