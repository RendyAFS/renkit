export function crudMessage(action, modelKey) {
    const template = window.langCrud?.[action] ?? "";
    const model = window.langModels?.[modelKey] ?? modelKey;

    return template.replace(":model", model);
}
