import "./utils/suppress-hsdatatable-warning";
import "./bootstrap";
import "preline";
import * as FloatingUIDOM from "@floating-ui/dom";
import initFormEnterSubmit from "./utils/form-enter-submit";
window.FloatingUIDOM = FloatingUIDOM;

// utils
import "./utils/sidebar-mode";
import "./utils/toggle-dark-mode";
import "./utils/custom-alert";
import "./utils/custom-select";
import "./utils/button-group";
import "./utils/number-input";
import "./utils/flatpickr-init";

import disableNumberScroll from "./utils/disable-number-scroll";
import initUi from "./utils/ui-init";
import initSplashScreen from "./utils/splash-screen";

initSplashScreen();

document.addEventListener("DOMContentLoaded", () => {
    initUi();
    disableNumberScroll();
    initFormEnterSubmit();
    if (window.Alpine) {
        window.Alpine.start();
    }
});
