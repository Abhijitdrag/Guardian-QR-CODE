// This file contains the JavaScript code that adds interactivity to the sign-up page. 
// It handles form validation, password strength indication, and animations for form submission and entrance effects.

document.addEventListener("DOMContentLoaded", () => {
    const signupForm = document.getElementById("signupForm");
    const successCheck = document.querySelector(".success-check");
    const strengthBar = document.querySelector(".strength-bar");

    // Form validation
    document.querySelectorAll("input").forEach((input) => {
        input.addEventListener("input", function () {
            const parent = this.parentElement;
            if (this.checkValidity()) {
                parent.classList.add("valid");
                parent.classList.remove("invalid");
            } else {
                parent.classList.add("invalid");
                parent.classList.remove("valid");
            }

            // Password strength
            if (this.id === "password") {
                const strength = Math.min((this.value.length / 12) * 100, 100);
                strengthBar.style.width = strength + "%";
                strengthBar.style.backgroundColor = `hsl(0, ${strength}%, ${100 - strength}%)`;
            }
        });
    });

    // Form submission
    signupForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const card = document.querySelector(".login-card");
        card.style.opacity = "0";
        card.style.transform = "translateY(-50px)";
        successCheck.style.display = "block";
        anime({
            targets: successCheck,
            opacity: [0, 1],
            duration: 800,
        });
    });

    // Animate card entrance
    anime({
        targets: ".login-card",
        opacity: [0, 1],
        translateY: [50, 0],
        duration: 1000,
        easing: "easeOutExpo",
        delay: 300,
    });
});