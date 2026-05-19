import { createDynamicList } from "./dynamicList";

document.addEventListener("DOMContentLoaded", () => {
    createDynamicList({
        containerId: "tags-container",
        inputName: "tags[]",
        validate: (value) => {
            if (!value) {
                return "Tag cannot be empty.";
            }
            if (value.length > 20) {
                return "Tag cannot exceed 20 characters.";
            }
            return null;
        },
    });
});
