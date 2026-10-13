const originalWarn = console.warn;
console.warn = (...args) => {
    if (typeof args[0] === "string" && args[0].includes("HSDataTable")) return;
    originalWarn(...args);
};
