interface DynamicListOptions {
    containerId: string;
    inputName: string;
    validate: (value: string) => string | null;
}

export function createDynamicList(options: DynamicListOptions): void {
    const container = document.getElementById(options.containerId);
    const input = container?.querySelector<HTMLInputElement>("input");
    const list = container?.querySelector<HTMLUListElement>("ul");
    const button = container?.querySelector<HTMLButtonElement>("button");
    const errorElement =
        container?.querySelector<HTMLParagraphElement>("[data-error]");

    if (!container || !input || !list || !button || !errorElement) {
        console.error(
            `Container with id ${options.containerId} is not properly structured.`,
        );
        return;
    }
    function handleClick() {
        const value = input!.value.trim();
        const error = options.validate(value ?? "");

        if (error) {
            return { ok: false, error };
        }
        return { ok: true, value: value };
    }

    button.addEventListener("click", () => {
        const result = handleClick();
        if (!result.ok && result.error) {
            showError(result.error);
        } else if (result.ok && result.value) {
            clearError();
            addItem(result.value);
            input.value = "";
        }
    });

    function showError(message: string) {
        if (errorElement && input) {
            errorElement.textContent = message;
            errorElement.classList.remove("hidden");
            input.classList.add("border-error");
        }
    }
    function clearError() {
        if (errorElement && input) {
            errorElement.textContent = "";
            errorElement.classList.add("hidden");
            input.classList.remove("border-error");
        }
    }
    function addItem(value: string) {
        if (!list) return;
        const li = createLI(value);
        const btn = createBtn();
        const hiddenInput = createHiddenInput(value);

        btn.addEventListener("click", () => {
            li.remove();
            hiddenInput.remove();
        });
        li.appendChild(btn);
        list.append(li, hiddenInput);
    }
    function createLI(value: string): HTMLLIElement {
        const li = document.createElement("li");
        li.textContent = value.length > 30 ? value.slice(0, 30) + "…" : value;
        li.classList.add(
            "text-sm",
            "text-text-secondary",
            "duration-200",
            "hover:text-error",
        );
        return li;
    }
    function createBtn(): HTMLButtonElement {
        const btn = document.createElement("button");
        btn.textContent = "x";
        btn.classList.add(
            "ml-2",
            "text-text-muted",
            "hover:text-error",
            "duration-200",
            "font-bold",
            "text-sm",
        );
        return btn;
    }
    function createHiddenInput(value: string): HTMLInputElement {
        const hiddenInput = document.createElement("input");
        hiddenInput.type = "hidden";
        hiddenInput.name = options.inputName;
        hiddenInput.value = value;
        return hiddenInput;
    }
    export { showError, clearError };
}
