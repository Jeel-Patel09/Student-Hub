let apiKey = "380b44f455acd0a5afb30794b2676b21";
let cityInput = document.getElementById("cityInput");
let searchButton = document.getElementById("searchWeather");
let weatherResult = document.getElementById("weatherResult");

searchButton.addEventListener("click", function () {
    let city = cityInput.value.trim();
    if (city === "") {
        weatherResult.innerHTML = "<p>Please enter a city name.</p>";
        return;
    }
    weatherResult.innerHTML = "<p>Loading weather...</p>";
    let url =
        "https://api.openweathermap.org/data/2.5/weather?q="
        + encodeURIComponent(city)
        + "&appid="
        + apiKey
        + "&units=metric";

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error("City not found");
            }
            return response.json();
        })
        .then(data => {
            weatherResult.innerHTML = `
                <div class="weather-card">
                    <h2>Live Weather</h2>
                    <p>
                        <strong>City:</strong>
                        ${data.name}, ${data.sys.country}
                    </p>
                    <p>
                        <strong>Temperature:</strong>
                        ${data.main.temp} °C
                    </p>
                    <p>
                        <strong>Weather:</strong>
                        ${data.weather[0].description}
                    </p>
                    <p>
                        <strong>Humidity:</strong>
                        ${data.main.humidity}%
                    </p>
                    <p>
                        <strong>Wind Speed:</strong>
                        ${data.wind.speed} m/s
                    </p>
                </div>
            `;
        })
        .catch(error => {
            weatherResult.innerHTML =
                "<p>City not found. Please enter a valid city name.</p>";
            console.log(error);
        });
});
