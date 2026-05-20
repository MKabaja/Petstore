import { createErrorHandler } from "./formErrorHandler";

interface DynamicListOptions {
    containerId: string;
    inputName: string;
    validate: (value: string) => string | null;
    existingDataKey?: string;
}

export function createDynamicList(options: DynamicListOptions): void {
    const container = document.getElementById(options.containerId);

    if (!container) {
        console.error(`Container with id "${options.containerId}" not found.`);
        return;
    }

    const errorElement =
        container.querySelector<HTMLParagraphElement>("[data-error]")!;
    const input = container.querySelector<HTMLInputElement>("input")!;
    const list = container.querySelector<HTMLUListElement>("ul")!;
    const button = container.querySelector<HTMLButtonElement>("button")!;

    if (!input || !list || !button || !errorElement) {
        console.error(
            `Container with id ${options.containerId} is not properly structured.`,
        );
        return;
    }

    const errorHandler = createErrorHandler(errorElement, input);

    function checkInput() {
        const value = input.value.trim();
        const error = options.validate(value);

        if (error) {
            return { ok: false as const, error };
        }

        return { ok: true as const, value: value };
    }

    button.addEventListener("click", () => {
        const result = checkInput();
        if (!result.ok && result.error) {
            errorHandler.show(result.error);
        } else if (result.ok && result.value) {
            errorHandler.clear();
            addItem(result.value);
            input.value = "";
        }
    });

    function addItem(value: string) {
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
    if (options.existingDataKey) {
        const raw = container.dataset[options.existingDataKey];
        if (raw) {
            try {
                const items: string[] = JSON.parse(raw);
                items.forEach((value) => addItem(value));
            } catch {
                console.error(`Failed to parse ${options.existingDataKey}`);
            }
        }
    }
}
