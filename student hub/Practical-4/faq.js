document.addEventListener("DOMContentLoaded", function() {

    const faqBoxes = document.querySelectorAll(".faq-box");

    faqBoxes.forEach(function(box) {

        const question = box.querySelector("h3");
        const answer = box.querySelector("p");

        answer.style.display = "none";
        question.style.cursor = "pointer";

        question.addEventListener("click", function() {

            if (answer.style.display === "none") {
                answer.style.display = "block";
            } else {
                answer.style.display = "none";
            }

        });
    });


    const nav = document.querySelector("nav ul");

    const themeButton = document.createElement("button");

    themeButton.innerHTML = "Dark Mode";
    themeButton.className = "theme-button";

    nav.appendChild(themeButton);


    if (localStorage.getItem("theme") === "dark") {

        document.body.classList.add("dark-mode");
        themeButton.innerHTML = "Light Mode";

    }


    themeButton.addEventListener("click", function() {

        document.body.classList.toggle("dark-mode");

        if (document.body.classList.contains("dark-mode")) {

            localStorage.setItem("theme", "dark");
            themeButton.innerHTML = " Light Mode";

        } else {

            localStorage.setItem("theme", "light");
            themeButton.innerHTML = " Dark Mode";

        }

    });

});