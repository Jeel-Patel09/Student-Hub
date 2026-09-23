let events = [];
let currentPage = 1;
let recordsPerPage = 5;

let search = document.getElementById("search");
let category = document.getElementById("category");
let sort = document.getElementById("sort");
let eventList = document.getElementById("eventList");
let loading = document.getElementById("loading");
let error = document.getElementById("error");

fetch("events.json")
.then(response => {
    if (!response.ok) {
        throw new Error("JSON file not found");
    }
    return response.json();
})
.then(data => {
    events = data;
    loading.style.display = "none";
    displayEvents();
})
.catch(err => {
    loading.style.display = "none";
    error.innerHTML = "Error loading event data";
});

function displayEvents() {

    let result = events.filter(event =>
        event.title.toLowerCase().includes(search.value.toLowerCase()) &&
        (category.value === "all" || event.category === category.value)
    );

    result.sort((a, b) => {
        if (sort.value === "title") {
            return a.title.localeCompare(b.title);
        }
        return new Date(a.date) - new Date(b.date);
    });

    let totalPages = Math.ceil(result.length / recordsPerPage);

    if (totalPages === 0) {
        totalPages = 1;
    }

    if (currentPage > totalPages) {
        currentPage = totalPages;
    }

    let start = (currentPage - 1) * recordsPerPage;
    let pageData = result.slice(start, start + recordsPerPage);

    eventList.innerHTML = "";

    pageData.map(event => {

        let card = document.createElement("div");

        card.className = "card";

        card.innerHTML = `
            <h2>${event.title}</h2>
            <p>Category: ${event.category}</p>
            <p>Date: ${event.date}</p>
            <p>Venue: ${event.venue}</p>
        `;

        eventList.appendChild(card);
    });

    if (pageData.length === 0) {
        eventList.innerHTML = "<p>No events found</p>";
    }

    document.getElementById("page").innerHTML =
        "Page " + currentPage + " of " + totalPages;

    document.getElementById("prev").disabled = currentPage === 1;
    document.getElementById("next").disabled = currentPage === totalPages;
}

search.addEventListener("input", function() {
    currentPage = 1;
    displayEvents();
});

category.addEventListener("change", function() {
    currentPage = 1;
    displayEvents();
});

sort.addEventListener("change", function() {
    currentPage = 1;
    displayEvents();
});

document.getElementById("prev").addEventListener("click", function() {
    currentPage--;
    displayEvents();
});

document.getElementById("next").addEventListener("click", function() {
    currentPage++;
    displayEvents();
});