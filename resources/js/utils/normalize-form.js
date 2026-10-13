/**
 * @param {HTMLFormElement} form
 * @param {Object} data
 * @returns {Object}
 */
function normalizeFormInputs(form, data) {

    $(form)
        .find('input[type="checkbox"][name]')
        .each(function () {
            const name = this.name;
            const checked = this.checked;

            const match = name.match(/^(\w+)\[(\w+)\]$/);

            if (match) {
                const parent = match[1];
                const key = match[2];

                if (!data[parent] || typeof data[parent] !== "object") {
                    data[parent] = {};
                }

                data[parent][key] = checked ? 1 : 0;
            } else {
                data[name] = checked ? 1 : 0;
            }
        });

    $(form)
        .find('input[type="radio"][name]')
        .each(function () {
            const name = this.name;

            if (!(name in data)) {
                const checked = $(form).find(`input[type="radio"][name="${name}"]:checked`).val();
                data[name] = checked ?? null;
            }
        });

    $(form)
        .find('input[type="file"][name]')
        .each(function () {
            const name = this.name;
            const files = this.files;

            data[name] = files && files.length > 0 ? files[0] : null;
        });

    $(form)
        .find('input[name]:not([type="checkbox"]):not([type="radio"]):not([type="file"]), textarea[name], select[name]')
        .each(function () {
            const name = this.name;
            let val = $(this).val();

            if (Array.isArray(val)) {
                data[name] = val.length ? val : null;
            } else {
                data[name] = val === "" || val === undefined ? null : val;
            }
        });

    return data;
}

export default normalizeFormInputs;
