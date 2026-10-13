function trans(bag, key, replacements = {}) {
    const dict = window[bag] || {};

    let text = key
        .split(".")
        .reduce((obj, part) => obj?.[part], dict);

    if (text == null) {
        return key;
    }

    Object.entries(replacements).forEach(([k, v]) => {
        text = text.replaceAll(`:${k}`, String(v));
    });

    return text;
}

export default trans;
