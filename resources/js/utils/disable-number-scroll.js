export default function disableNumberScroll() {
    document.addEventListener(
        "wheel",
        (e) => {
            const target = e.target;

            if (
                target instanceof HTMLInputElement &&
                target.type === "number"
            ) {
                e.preventDefault();
            }
        },
        { passive: false },
    );
}
