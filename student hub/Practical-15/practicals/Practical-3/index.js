document.addEventListener("DOMContentLoaded", function () {
    const signout = document.getElementById("btn");

    signout.addEventListener("click", function () {
        alert("You have successfully logged out!");
        window.location.href = "login.html";
    });
});