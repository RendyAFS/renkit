export default function initSplashScreen(minDuration = 1200) {
    const splash = document.getElementById("splash-screen");
    if (!splash) return;

    document.documentElement.classList.add("splash-lock");

    const start = Date.now();

    const hide = () => {
        const elapsed = Date.now() - start;
        const remaining = Math.max(minDuration - elapsed, 0);
        setTimeout(() => {
            splash.classList.add("splash-hide");
            document.documentElement.classList.remove("splash-lock");
            setTimeout(() => splash.remove(), 500);
        }, remaining);
    };

    if (document.readyState === "complete") {
        hide();
    } else {
        window.addEventListener("load", hide);
    }
}
