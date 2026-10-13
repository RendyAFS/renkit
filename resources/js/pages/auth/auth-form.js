import { startLoading } from "@/utils/button-loading";

export default function initAuthForm(selector = "form[data-auth-form]") {
    document.querySelectorAll(selector).forEach((form) => {
        form.addEventListener("submit", (e) => {
            const submitter = e.submitter;
            if (!submitter) return;

            if (submitter.hasAttribute("data-button-loading")) {
                startLoading(submitter);
            }
        });
    });
}

if (document.readyState !== "loading") {
    initAuthForm();
} else {
    document.addEventListener("DOMContentLoaded", () => initAuthForm());
}
