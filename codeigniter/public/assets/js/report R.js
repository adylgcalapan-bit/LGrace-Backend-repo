/*
=========================================
Report a Problem
Community Problems Visibility System
=========================================
*/

document.addEventListener("DOMContentLoaded", function () {

    const reportForm = document.getElementById("reportForm");
    const titleInput = document.getElementById("title");
    const titleError = document.getElementById("titleError");
    const categorySelect = document.getElementById("category");
    const categoryError = document.getElementById("categoryError");
    const descriptionInput = document.getElementById("description");
    const descriptionError = document.getElementById("descriptionError");
    const latitudeInput = document.getElementById("latitude");
    const longitudeInput = document.getElementById("longitude");
    const mapContainer = document.getElementById("map");
    const locationStatus = document.getElementById("locationStatus");
    const locationError = document.getElementById("locationError");
    const formSuccess = document.getElementById("formSuccess");
    const useLocationBtn = document.getElementById("useLocationBtn");
    const resetButton = document.querySelector('button[type="reset"]');

    // =====================================
    // Initialize Map
    // =====================================

    if (mapContainer && typeof L !== "undefined") {
        const map = L.map("map").setView([7.0083, 125.0894], 13);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors"
        }).addTo(map);

        let marker;

        function setMarker(lat, lng) {
            map.setView([lat, lng], 16);

            if (marker) {
                map.removeLayer(marker);
            }

            marker = L.marker([lat, lng]).addTo(map);

            if (latitudeInput) latitudeInput.value = lat.toFixed(6);
            if (longitudeInput) longitudeInput.value = lng.toFixed(6);

            if (locationStatus) {
                locationStatus.textContent = `Location selected: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            }

            setLocationState(true);
        }

        setMarker(7.0083, 125.0894);

        if (useLocationBtn) {
            useLocationBtn.addEventListener("click", function () {
                if (!navigator.geolocation) {
                    if (locationStatus) {
                        locationStatus.textContent = "Geolocation is not supported by this browser.";
                    }
                    return;
                }

                useLocationBtn.disabled = true;
                useLocationBtn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Getting location...';

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        setMarker(lat, lng);
                        useLocationBtn.disabled = false;
                        useLocationBtn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Use My Current Location';
                    },
                    function () {
                        if (locationStatus) {
                            locationStatus.textContent = "Unable to get your current location. Please click the map manually.";
                        }
                        setLocationState(false, "Please pin the exact location on the map.");
                        useLocationBtn.disabled = false;
                        useLocationBtn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Use My Current Location';
                    }
                );
            });
        }

        map.on("click", function (e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            setMarker(lat, lng);
        });
    }

    // =====================================
    // Report Form Validation
    // =====================================

    function setFieldState(input, feedback, isValid, message = "") {
        if (!input) return;

        input.classList.toggle("is-invalid", !isValid);
        input.classList.toggle("is-valid", isValid && input.value.trim() !== "");

        if (feedback) {
            feedback.textContent = message;
            feedback.classList.toggle("show", !isValid);
        }
    }

    function setLocationState(isValid, message = "") {
        if (mapContainer) {
            mapContainer.classList.toggle("map-invalid", !isValid);
            mapContainer.classList.toggle("map-valid", isValid);
        }

        if (locationError) {
            locationError.textContent = message;
            locationError.classList.toggle("show", !isValid);
        }

        if (locationStatus) {
            locationStatus.classList.toggle("text-danger", !isValid);
            locationStatus.classList.toggle("text-success", isValid);
        }
    }

    function clearFormFeedback() {
        if (formSuccess) {
            formSuccess.classList.remove("show");
            formSuccess.textContent = "";
        }

        setFieldState(titleInput, titleError, true);
        setFieldState(categorySelect, categoryError, true);
        setFieldState(descriptionInput, descriptionError, true);
        setLocationState(true);
    }

    function showSuccessMessage(message) {
        if (formSuccess) {
            formSuccess.textContent = message;
            formSuccess.classList.add("show");
        }
    }

    function validateTitle() {
        const isValid = titleInput ? titleInput.value.trim() !== "" : true;
        setFieldState(titleInput, titleError, isValid, isValid ? "" : "Please enter a report title.");
        return isValid;
    }

    function validateCategory() {
        const isValid = categorySelect ? categorySelect.value !== "" && categorySelect.value !== "Select Category" : true;
        setFieldState(categorySelect, categoryError, isValid, isValid ? "" : "Please select a category.");
        return isValid;
    }

    function validateDescription() {
        const isValid = descriptionInput ? descriptionInput.value.trim() !== "" : true;
        setFieldState(descriptionInput, descriptionError, isValid, isValid ? "" : "Please provide a description.");
        return isValid;
    }

    function validateLocation() {
        const hasLocation = latitudeInput && longitudeInput && latitudeInput.value !== "" && longitudeInput.value !== "";
        setLocationState(hasLocation, hasLocation ? "" : "Please pin the exact location on the map.");
        return hasLocation;
    }

    if (titleInput) {
        titleInput.addEventListener("input", validateTitle);
    }

    if (categorySelect) {
        categorySelect.addEventListener("change", validateCategory);
    }

    if (descriptionInput) {
        descriptionInput.addEventListener("input", validateDescription);
    }

    if (reportForm) {
        reportForm.addEventListener("submit", function (event) {
            event.preventDefault();

            const titleValid = validateTitle();
            const categoryValid = validateCategory();
            const descriptionValid = validateDescription();
            const locationValid = validateLocation();

            if (!titleValid || !categoryValid || !descriptionValid || !locationValid) {
                return;
            }

            showSuccessMessage("Report submitted successfully. Your report is ready for review.");

            reportForm.reset();
            clearFormFeedback();
            if (latitudeInput) latitudeInput.value = "";
            if (longitudeInput) longitudeInput.value = "";
        });
    }

    // =====================================
    // Reset Map Marker
    // =====================================

    if (resetButton) {
        resetButton.addEventListener("click", function () {
            if (latitudeInput) latitudeInput.value = "";
            if (longitudeInput) longitudeInput.value = "";
            clearFormFeedback();
            setLocationState(false, "Please pin the exact location on the map.");
        });
    }

});

/*
=========================================
Future Backend Integration
=========================================

fetch("/report", {
    method: "POST",
    body: formData
});

The backend developer will connect this
to CodeIgniter and MySQL.

=========================================
*/