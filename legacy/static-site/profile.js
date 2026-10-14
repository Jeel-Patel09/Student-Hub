document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("profileForm");
    form.addEventListener("submit", function(event) {
        event.preventDefault();
        if (form.checkValidity()) {

            alert("You have successfully updated your profile.");

            setTimeout(function() {
                window.location.href = "dashboard.html";
            }, 1000);
        }
    });

});