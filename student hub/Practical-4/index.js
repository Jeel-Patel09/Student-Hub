document.addEventListener("DOMContentLoaded", function () {
    const logoutBtn = document.getElementById("btn");

    if (logoutBtn) {
        logoutBtn.addEventListener("click", function () {
            alert("You have been logged out successfully!");
            window.location.href = "login.html";
        });
    }
    const navList = document.querySelector("nav ul");
    const themeBtn = document.createElement("button");
    themeBtn.id = "themeBtn";
    themeBtn.textContent = "Dark Mode";
    themeBtn.type = "button";

    navList.appendChild(themeBtn);
    const savedTheme = localStorage.getItem("theme");

    if (savedTheme === "dark") {
        document.body.classList.add("dark-mode");
        themeBtn.textContent = "Light Mode";
    }
    themeBtn.addEventListener("click", function () {

        document.body.classList.toggle("dark-mode");

        if (document.body.classList.contains("dark-mode")) {

            localStorage.setItem("theme", "dark");
            themeBtn.textContent = "Light Mode";

        } else {

            localStorage.setItem("theme", "light");
            themeBtn.textContent = "Dark Mode";
        }
    });
    const notification = document.createElement("div");
    notification.id = "notification";
    notification.textContent = "Welcome to StudentHub Portal!";

    document.body.appendChild(notification);
    setTimeout(function () {

        notification.classList.add("show");
        setTimeout(function () {
            notification.classList.remove("show");
        }, 3000);

    }, 500);
    const modal = document.createElement("div");
    modal.id = "studentModal";
    modal.innerHTML = `
        <div class="modal-content">

            <button id="closeModal" class="close-modal">
                X
            </button>

            <h2>Welcome to StudentHub</h2>

            <p>
                StudentHub helps students manage attendance,
                assignments, results, events and other academic
                activities from one place.
            </p>

        </div>
    `;

    document.body.appendChild(modal);
    const registerBtn = document.querySelector(".hero .btn");

    if (registerBtn) {

        registerBtn.addEventListener("click", function (event) {
            event.preventDefault();
            modal.classList.add("show-modal");
        });
    }
    const closeModal = document.getElementById("closeModal");
    closeModal.addEventListener("click", function () {

        modal.classList.remove("show-modal");

    });

    modal.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.classList.remove("show-modal");
        }

    });
    document.addEventListener("keydown", function (event) {

        if (event.key === "Escape") {
            modal.classList.remove("show-modal");
        }

    });
    const boxes = document.querySelectorAll(".features .box");
    let currentSlide = 0;

    if (boxes.length > 0) {
        document.querySelector(".features").classList.add("slider-mode");
        boxes.forEach(function (box, index) {

            if (index === 0) {
                box.classList.add("active-box");
            } else {
                box.classList.remove("active-box");
            }

        });
        const sliderControls = document.createElement("div");
        sliderControls.className = "slider-controls";
        sliderControls.innerHTML = `
            <button id="prevSlide">Previous</button>
            <button id="nextSlide">Next</button>
        `;
        document.querySelector(".features").appendChild(sliderControls);
        function showSlide(index) {

            boxes.forEach(function (box) {
                box.classList.remove("active-box");
            });

            boxes[index].classList.add("active-box");
        }
        document.getElementById("prevSlide").addEventListener("click", function () {

            currentSlide--;

            if (currentSlide < 0) {
                currentSlide = boxes.length - 1;
            }

            showSlide(currentSlide);
        });
        document.getElementById("nextSlide").addEventListener("click", function () {
            currentSlide++;
            if (currentSlide >= boxes.length) {
                currentSlide = 0;
            }

            showSlide(currentSlide);
        });

    }
    boxes.forEach(function (box) {

        box.addEventListener("click", function () {
            const heading = box.querySelector("h3");
            if (heading) {

                notification.textContent =
                    heading.textContent + " section selected!";

                notification.classList.add("show");

                setTimeout(function () {
                    notification.classList.remove("show");
                }, 2000);
            }

        });

    });

});