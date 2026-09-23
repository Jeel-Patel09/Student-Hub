document.addEventListener("DOMContentLoaded", function () {
    const nav = document.querySelector("nav ul");

    const themeButton = document.createElement("button");

    themeButton.className = "theme-button";
    themeButton.innerHTML = "Dark Mode";

    nav.appendChild(themeButton);
    const savedTheme = localStorage.getItem("dashboardTheme");

    if (savedTheme === "dark") {
        document.body.classList.add("dark-mode");
        themeButton.innerHTML = "Light Mode";
    }
    themeButton.addEventListener("click", function () {

        document.body.classList.toggle("dark-mode");

        if (document.body.classList.contains("dark-mode")) {

            localStorage.setItem("dashboardTheme", "dark");

            themeButton.innerHTML = " Light Mode";

        } else {

            localStorage.setItem("dashboardTheme", "light");

            themeButton.innerHTML = "Dark Mode";
        }

    });
    const notification = document.createElement("div");
    notification.className = "notification";

    notification.innerHTML =
        "Welcome to your StudentHub Dashboard!";

    const dashboard = document.querySelector(".dashboard");

    dashboard.insertBefore(
        notification,
        dashboard.querySelector("h2")
    );
    setTimeout(function () {

        notification.style.display = "none";

    }, 4000);
    const cards = document.querySelectorAll(".card");
    cards.forEach(function (card) {
        card.addEventListener("click", function () {
            const title = card.querySelector("h3").innerText;
            const value = card.querySelector("p").innerText;

            alert(title + ": " + value);

        });

    });
    cards.forEach(function (card) {

        card.addEventListener("mouseenter", function () {

            card.style.transform = "scale(1.03)";

        });

        card.addEventListener("mouseleave", function () {

            card.style.transform = "scale(1)";

        });

    });
    const notices = document.querySelectorAll(".notice li");

    notices.forEach(function (notice) {

        notice.addEventListener("click", function () {

            alert("Notice: " + notice.innerText);

        });

        notice.style.cursor = "pointer";

    });
    const studentInfo = document.querySelector(".student");

    studentInfo.addEventListener("click", function () {

        alert("Student information section selected.");

    });
    const menuButton = document.createElement("button");
    menuButton.className = "menu-button";
    menuButton.innerHTML = "☰ Menu";
    document.querySelector("header").appendChild(menuButton);

    menuButton.addEventListener("click", function () {

        nav.classList.toggle("show-menu");

    });
    const navLinks = document.querySelectorAll("nav ul li a");
    navLinks.forEach(function (link) {

        link.addEventListener("click", function () {

            nav.classList.remove("show-menu");

        });

    });

});