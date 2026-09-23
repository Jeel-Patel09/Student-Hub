let form = document.querySelector("form");
let password = document.querySelectorAll("input")[2];

let strength = document.createElement("p");
password.after(strength);

password.addEventListener("input", function() {
    let pass = password.value;

    let weak = /^.{1,5}$/;
    let medium = /^(?=.*[A-Za-z])(?=.*[0-9]).{6,}$/;
    let strong = /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}$/;

    if (pass == "") {
        strength.innerHTML = "";
    }
    else if (weak.test(pass)) {
        strength.innerHTML = "Weak Password";
    }
    else if (strong.test(pass)) {
        strength.innerHTML = "Strong Password";
    }
    else if (medium.test(pass)) {
        strength.innerHTML = "Medium Password";
    }
    else {
        strength.innerHTML = "Weak Password";
    }
});

form.addEventListener("submit", function(event) {
    let name = document.querySelectorAll("input")[0].value;
    let email = document.querySelectorAll("input")[1].value;
    let pass = password.value;
    let confirmPassword = document.querySelectorAll("input")[3].value;

    let nameRegex = /^[A-Za-z ]+$/;
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}$/;

    if (!nameRegex.test(name)) {
        alert("Enter a valid name");
        event.preventDefault();
    }
    else if (!emailRegex.test(email)) {
        alert("Enter a valid email");
        event.preventDefault();
    }
    else if (!passwordRegex.test(pass)) {
        alert("Enter a strong password");
        event.preventDefault();
    }
    else if (pass != confirmPassword) {
        alert("Passwords do not match");
        event.preventDefault();
    }
    else {
        alert("Registration successful!");
    }
});