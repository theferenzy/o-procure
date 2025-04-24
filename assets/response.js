document.addEventListener("DOMContentLoaded", function () {
    const alertBox = document.querySelector(".alert-message");
    const formFields = document.querySelectorAll("input, textarea, select");

    // Fade out alert messages after 5 seconds
    if (alertBox) {
        setTimeout(() => {
            alertBox.style.transition = "opacity 0.5s ease";
            alertBox.style.opacity = "0";
        }, 5000);
    }

    formFields.forEach(field => {
        field.addEventListener("blur", () => {
            if (!field.value.trim()) {
                field.classList.add("border-red-500");
            } else {
                field.classList.remove("border-red-500");
            }
        });
    });

    const secureForms = document.querySelectorAll("form.secure-form");
    secureForms.forEach(form => {
        form.addEventListener("submit", function (e) {
            const confirmed = confirm("Are you sure you want to proceed?");
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });
});
