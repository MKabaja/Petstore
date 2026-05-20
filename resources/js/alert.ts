document.addEventListener("DOMContentLoaded", () => {
    const alerts = document.querySelectorAll("[data-alert]");

    alerts.forEach((alert) => {
        setTimeout(() => {
            alert.remove();
        }, 3000);
    });
});
