import { initLucide } from "./lucide";
import RupiahInput from "./rupiah-input";

export default function initUi() {
    window.HSStaticMethods?.autoInit([
        "accessibility-observer",
        "accordion",
        "carousel",
        "collapse",
        "combo-box",
        "copy-markup",
        "dropdown",
        "file-upload",
        "input-number",
        "layout-splitter",
        "overlay",
        "pin-input",
        "range-slider",
        "remove-element",
        "scroll-nav",
        "scrollspy",
        "select",
        "stepper",
        "strong-password",
        "tabs",
        "textarea-auto-height",
        "theme-switch",
        "toggle-count",
        "toggle-password",
        "tooltip",
        "tree-view",
    ]);
    initLucide();
    RupiahInput.init();
}
