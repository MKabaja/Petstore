export function createErrorHandler(
    errorElement: HTMLElement,
    input: HTMLInputElement,
) {
    return {
        show(message: string) {
            errorElement.textContent = message;
            errorElement.classList.remove("hidden");
            input.classList.add("border-error");
        },
        clear() {
            errorElement.textContent = "";
            errorElement.classList.add("hidden");
            input.classList.remove("border-error");
        },
    };
}
