(function () {
    try {
        var params = new URLSearchParams(window.location.search);
        var hasValues = [...params.entries()].some(function (entry) {
            return entry[1] !== "";
        });
        if (hasValues) return;

        var key = "filters:" + window.location.pathname;
        var stored = sessionStorage.getItem(key);

        if (stored) {
            window.location.replace(window.location.pathname + "?" + stored);
        }
    } catch (e) {}
})();
