import { createDynamicList } from "./dynamicList";

document.addEventListener("DOMContentLoaded", () => {
    createDynamicList({
        containerId: "photos-container",
        inputName: "photo_urls[]",
        validate: (value) => {
            try {
                new URL(value);
                return null;
            } catch {
                return "Must be a valid URL.";
            }
        },
    });
});
