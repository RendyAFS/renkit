(() => {
    const html = document.documentElement;
    const storedTheme = localStorage.getItem("hs_theme") || "auto";
    const prefersDark = window.matchMedia(
        "(prefers-color-scheme: dark)",
    ).matches;

    const isDark =
        storedTheme === "dark" || (storedTheme === "auto" && prefersDark);

    html.classList.toggle("dark", isDark);
    html.classList.toggle("light", !isDark);
})();
