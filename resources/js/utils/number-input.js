const NumberInput = {
    increment(value, step = 1) {
        return Number(value || 0) + step;
    },

    decrement(value, min = 0, step = 1) {
        return Math.max(min, Number(value || 0) - step);
    },

    normalize(value, min = 0) {
        if (value === "" || value === null || value === undefined) {
            return min;
        }

        const number = Number(value);

        if (Number.isNaN(number)) {
            return min;
        }

        return Math.max(min, number);
    },
};

window.NumberInput = NumberInput;

export default NumberInput;
