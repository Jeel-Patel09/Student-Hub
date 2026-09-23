let questions = document.querySelectorAll(".faq-box h3");

questions.forEach(function(question) {

    question.addEventListener("click", function() {

        let answer = this.nextElementSibling;

        if (answer.style.display === "none") {
            answer.style.display = "block";
        } else {
            answer.style.display = "none";
        }

    });

});