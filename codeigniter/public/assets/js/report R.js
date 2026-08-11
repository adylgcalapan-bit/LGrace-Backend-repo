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
  const addressInput = document.getElementById("address");

  const mapContainer = document.getElementById("map");
  const locationStatus = document.getElementById("locationStatus");
  const locationError = document.getElementById("locationError");

  const useLocationBtn = document.getElementById("useLocationBtn");
  const resetButton = document.querySelector('button[type="reset"]');

  let map = null;
  let marker = null;

  // =====================================
  // Location Validation State
  // =====================================

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

  // =====================================
  // Initialize Map
  // =====================================

  if (mapContainer && typeof L !== "undefined") {
    map = L.map("map").setView([7.0083, 125.0894], 13);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: "&copy; OpenStreetMap contributors",
    }).addTo(map);

    async function getAddressFromCoordinates(lat, lng) {
      if (!addressInput) {
        return;
      }

      addressInput.value = "Getting address...";

      try {
        const url =
          `https://nominatim.openstreetmap.org/reverse` +
          `?format=jsonv2` +
          `&lat=${encodeURIComponent(lat)}` +
          `&lon=${encodeURIComponent(lng)}` +
          `&zoom=18` +
          `&addressdetails=1`;

        const response = await fetch(url);

        if (!response.ok) {
          throw new Error("Unable to get address.");
        }

        const data = await response.json();

        addressInput.value = data.display_name || "Address not available";
      } catch (error) {
        console.error("Reverse geocoding error:", error);

        addressInput.value = "Address not available";
      }
    }

    function setMarker(lat, lng) {
      map.setView([lat, lng], 16);

      if (marker) {
        map.removeLayer(marker);
      }

      marker = L.marker([lat, lng]).addTo(map);

      if (latitudeInput) {
        latitudeInput.value = lat.toFixed(6);
      }

      if (longitudeInput) {
        longitudeInput.value = lng.toFixed(6);
      }

      getAddressFromCoordinates(lat, lng);

      if (locationStatus) {
        locationStatus.textContent = `Location selected: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
      }

      setLocationState(true);
    }

    // =====================================
    // Use Current Location
    // =====================================

    if (useLocationBtn) {
      useLocationBtn.addEventListener("click", function (event) {
        event.preventDefault();

        if (!navigator.geolocation) {
          if (locationStatus) {
            locationStatus.textContent =
              "Geolocation is not supported by this browser.";
          }

          return;
        }

        useLocationBtn.disabled = true;

        useLocationBtn.innerHTML =
          '<i class="bi bi-geo-alt-fill"></i> Getting location...';

        navigator.geolocation.getCurrentPosition(
          function (position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            setMarker(lat, lng);

            useLocationBtn.disabled = false;

            useLocationBtn.innerHTML =
              '<i class="bi bi-geo-alt-fill"></i> Use My Current Location';
          },

          function () {
            if (locationStatus) {
              locationStatus.textContent =
                "Unable to get your current location. Please click the map manually.";
            }

            setLocationState(
              false,
              "Please pin the exact location on the map.",
            );

            useLocationBtn.disabled = false;

            useLocationBtn.innerHTML =
              '<i class="bi bi-geo-alt-fill"></i> Use My Current Location';
          },
        );
      });
    }

    // =====================================
    // Click Map to Select Location
    // =====================================

    map.on("click", function (event) {
      const lat = event.latlng.lat;
      const lng = event.latlng.lng;

      setMarker(lat, lng);
    });
  }

  // =====================================
  // Form Field Validation
  // =====================================

  function setFieldState(input, feedback, isValid, message = "") {
    if (!input) {
      return;
    }

    input.classList.toggle("is-invalid", !isValid);

    input.classList.toggle("is-valid", isValid && input.value.trim() !== "");

    if (feedback) {
      feedback.textContent = message;

      feedback.classList.toggle("show", !isValid);
    }
  }

  function clearFormFeedback() {
    setFieldState(titleInput, titleError, true);

    setFieldState(categorySelect, categoryError, true);

    setFieldState(descriptionInput, descriptionError, true);
  }

  function validateTitle() {
    const isValid = titleInput ? titleInput.value.trim() !== "" : true;

    setFieldState(
      titleInput,
      titleError,
      isValid,
      isValid ? "" : "Please enter a report title.",
    );

    return isValid;
  }

  function validateCategory() {
    const isValid = categorySelect ? categorySelect.value !== "" : true;

    setFieldState(
      categorySelect,
      categoryError,
      isValid,
      isValid ? "" : "Please select a category.",
    );

    return isValid;
  }

  function validateDescription() {
    const isValid = descriptionInput
      ? descriptionInput.value.trim() !== ""
      : true;

    setFieldState(
      descriptionInput,
      descriptionError,
      isValid,
      isValid ? "" : "Please provide a description.",
    );

    return isValid;
  }

  function validateLocation() {
    const hasLocation =
      latitudeInput &&
      longitudeInput &&
      latitudeInput.value !== "" &&
      longitudeInput.value !== "";

    setLocationState(
      hasLocation,
      hasLocation ? "" : "Please pin the exact location on the map.",
    );

    return hasLocation;
  }

  // =====================================
  // Live Validation
  // =====================================

  if (titleInput) {
    titleInput.addEventListener("input", validateTitle);
  }

  if (categorySelect) {
    categorySelect.addEventListener("change", validateCategory);
  }

  if (descriptionInput) {
    descriptionInput.addEventListener("input", validateDescription);
  }

  // =====================================
  // Submit Report Form
  // =====================================

  if (reportForm) {
    reportForm.addEventListener("submit", function (event) {
      const titleValid = validateTitle();

      const categoryValid = validateCategory();

      const descriptionValid = validateDescription();

      const locationValid = validateLocation();

      if (
        !titleValid ||
        !categoryValid ||
        !descriptionValid ||
        !locationValid
      ) {
        event.preventDefault();
      }
    });
  }

  // =====================================
  // Reset Form and Map
  // =====================================

  if (resetButton) {
    resetButton.addEventListener("click", function () {
      if (latitudeInput) {
        latitudeInput.value = "";
      }

      if (longitudeInput) {
        longitudeInput.value = "";
      }

      if (map && marker) {
        map.removeLayer(marker);
        marker = null;
      }

      if (map) {
        map.setView([7.0083, 125.0894], 13);
      }

      clearFormFeedback();

      if (locationStatus) {
        locationStatus.textContent =
          "Click the button to use your current location or click on the map to choose a location.";
      }

      setLocationState(false, "Please pin the exact location on the map.");
    });
  }
});
