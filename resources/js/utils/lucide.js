import { createIcons, icons } from "lucide";

export function initLucide(root = document) {
    createIcons({
        icons,
        attrs: {
            "stroke-width": 1.75,
        },
        root,
    });
}

if (typeof window !== "undefined") {
    window.lucide = {
        createIcons: (opts = {}) => {
            createIcons({
                icons,
                attrs: {
                    "stroke-width": 1.75,
                },
                root: opts.root || document,
                ...opts,
            });
        },
    };
}

