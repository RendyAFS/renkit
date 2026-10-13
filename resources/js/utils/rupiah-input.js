const RupiahInput = {
    selector: "[data-rupiah]",

    init() {
        document.querySelectorAll(this.selector).forEach((input) => {
            this.apply(input);
        });
    },

    apply(input) {
        if (input.dataset.rupiahInitialized) return;

        if (input.value) {
            input.value = this.format(input.value);
        }

        input.addEventListener("input", (e) => {
            const raw = this.unformat(e.target.value);
            e.target.value = this.format(raw);
            e.target.dataset.raw = raw;
        });

        input.form?.addEventListener("submit", () => {
            input.value = this.unformat(input.value);
        });

        input.dataset.rupiahInitialized = true;
    },

    refresh(input) {
        input.value = this.format(input.value);
    },

    format(value) {
        if (!value) return "";

        const number = value.toString().replace(/\D/g, "");
        if (!number) return "";

        return new Intl.NumberFormat("id-ID").format(number);
    },

    unformat(value) {
        return value.toString().replace(/\D/g, "");
    },
};

export default RupiahInput;
