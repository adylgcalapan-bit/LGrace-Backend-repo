document.addEventListener("DOMContentLoaded", function () {
  const reportForm = document.getElementById("reportForm");
  const titleInput = document.getElementById("title");
  const titleError = document.getElementById("titleError");

  const categorySelect = document.getElementById("category");
  const categoryError = document.getElementById("categoryError");

  const descriptionInput = document.getElementById("description");
  const descriptionError = document.getElementById("descriptionError");

  const incidentDateInput = document.getElementById("incidentDate");
  const incidentDateError = document.getElementById("incidentDateError");

  const photosInput = document.getElementById("photos");
  const photosError = document.getElementById("photosError");
  const photoCount = document.getElementById("photoCount");

  const latitudeInput = document.getElementById("latitude");
  const longitudeInput = document.getElementById("longitude");
  const addressInput = document.getElementById("address");
  const reportPurokSelect = document.getElementById("reportPurok");

  const mapContainer = document.getElementById("map");
  const locationStatus = document.getElementById("locationStatus");
  const locationError = document.getElementById("locationError");

  const useLocationBtn = document.getElementById("useLocationBtn");
  const resetButton = document.querySelector('button[type="reset"]');

  const submitButton = reportForm
    ? reportForm.querySelector('button[type="submit"]')
    : null;

  let isSubmitting = false;

  function validateIncidentDate() {
    if (!incidentDateInput) {
      return true;
    }

    const value = incidentDateInput.value;
    const minDate = incidentDateInput.min;
    const maxDate = incidentDateInput.max;

    const originalDate = incidentDateInput.dataset.originalDate || "";
    const yesterday = incidentDateInput.dataset.yesterday || "";
    const today = incidentDateInput.dataset.today || "";

    let message = "";

    if (!value) {
      message = "Please select the incident date.";
    } else if (originalDate || yesterday || today) {
      // Edit Report:
      // original date OR yesterday OR today only.
      const allowedDates = [originalDate, yesterday, today].filter(Boolean);

      if (!allowedDates.includes(value)) {
        if (today && value > today) {
          message = "Incident date cannot be in the future.";
        } else {
          message =
            "You may keep the original report date or change it to today or yesterday only.";
        }
      }
    } else if (maxDate && value > maxDate) {
      // Add Report
      message = "Incident date cannot be in the future.";
    } else if (minDate && value < minDate) {
      // Add Report
      message = "Incident date can only be yesterday or today.";
    }

    const isValid = message === "";

    incidentDateInput.classList.toggle("is-invalid", !isValid);
    incidentDateInput.classList.toggle("is-valid", isValid);

    if (incidentDateError) {
      incidentDateError.textContent = message;
    }

    return isValid;
  }

  let map = null;
  let marker = null;

  const SAGUING_BOUNDS = {
    minLat: 6.955,
    maxLat: 6.995,
    minLng: 125.055,
    maxLng: 125.105,
  };

  function isInsideSaguingBounds(lat, lng) {
    return (
      lat >= SAGUING_BOUNDS.minLat &&
      lat <= SAGUING_BOUNDS.maxLat &&
      lng >= SAGUING_BOUNDS.minLng &&
      lng <= SAGUING_BOUNDS.maxLng
    );
  }

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
    map = L.map("map");

    const saguingLeafletBounds = L.latLngBounds(
      [SAGUING_BOUNDS.minLat, SAGUING_BOUNDS.minLng],
      [SAGUING_BOUNDS.maxLat, SAGUING_BOUNDS.maxLng],
    );

    map.fitBounds(saguingLeafletBounds, {
      padding: [20, 20],
    });

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: "&copy; OpenStreetMap contributors",
    }).addTo(map);

    // =====================================
    // Show existing location when editing
    // =====================================

    const existingLatitude = parseFloat(latitudeInput?.value);
    const existingLongitude = parseFloat(longitudeInput?.value);

    if (
      Number.isFinite(existingLatitude) &&
      Number.isFinite(existingLongitude)
    ) {
      marker = L.marker([existingLatitude, existingLongitude]).addTo(map);

      map.setView([existingLatitude, existingLongitude], 17);

      if (locationStatus) {
        locationStatus.textContent = `Current location: ${existingLatitude.toFixed(6)}, ${existingLongitude.toFixed(6)}`;
      }
    }

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

        const baseAddress = data.display_name || "Address not available";

        addressInput.dataset.baseAddress = baseAddress;
        addressInput.value = baseAddress;
      } catch (error) {
        console.error("Reverse geocoding error:", error);

        addressInput.value = "Address not available";
      }
    }
    function selectNearestPurok(lat, lng) {
      if (!reportPurokSelect) {
        return;
      }

      const MAX_PUROK_DISTANCE_METERS = 1000;

      let nearestOption = null;
      let nearestDistance = Infinity;

      Array.from(reportPurokSelect.options).forEach(function (option) {
        if (!option.value) {
          return;
        }

        const purokLat = parseFloat(option.dataset.latitude);
        const purokLng = parseFloat(option.dataset.longitude);

        if (!Number.isFinite(purokLat) || !Number.isFinite(purokLng)) {
          return;
        }

        const distance = L.latLng(lat, lng).distanceTo(
          L.latLng(purokLat, purokLng),
        );

        if (distance < nearestDistance) {
          nearestDistance = distance;
          nearestOption = option;
        }
      });

      if (nearestOption && nearestDistance <= MAX_PUROK_DISTANCE_METERS) {
        reportPurokSelect.value = nearestOption.value;
      } else {
        reportPurokSelect.value = "";
      }
    }

    function setMarker(lat, lng) {
      if (!isInsideSaguingBounds(lat, lng)) {
        if (locationStatus) {
          locationStatus.textContent =
            "Selected location is outside Barangay Saguing. Returning to Saguing...";
        }

        setLocationState(
          false,
          "Please select a location within Barangay Saguing, Makilala, Cotabato.",
        );

        if (latitudeInput) {
          latitudeInput.value = "";
        }

        if (longitudeInput) {
          longitudeInput.value = "";
        }

        if (addressInput) {
          addressInput.value = "";
        }

        if (reportPurokSelect) {
          reportPurokSelect.value = "";
        }

        // Automatically return the map to Barangay Saguing
        map.flyToBounds(saguingLeafletBounds, {
          padding: [20, 20],
          duration: 1.2,
        });

        return;
      }

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
      selectNearestPurok(lat, lng);

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
  // Photo Validation
  // Maximum: 5 photos, 5 MB each
  // =====================================

  function setPhotosState(isValid, message = "") {
    if (!photosInput) {
      return;
    }

    photosInput.classList.toggle("is-invalid", !isValid);
    photosInput.classList.toggle(
      "is-valid",
      isValid && photosInput.files.length > 0,
    );

    if (photosError) {
      photosError.textContent = message;
      photosError.classList.toggle("show", !isValid);
    }
  }

  function validatePhotos() {
    if (!photosInput) {
      return true;
    }

    const files = Array.from(photosInput.files || []);

    if (photoCount) {
      photoCount.textContent = `${files.length} of 5 photos selected`;
    }

    // Maximum 5 photos
    if (files.length > 5) {
      setPhotosState(false, "You can upload a maximum of 5 photos only.");

      return false;
    }

    const allowedTypes = ["image/jpeg", "image/png", "image/webp"];

    const maxFileSize = 5 * 1024 * 1024;

    for (const file of files) {
      if (!allowedTypes.includes(file.type)) {
        setPhotosState(false, "Only JPG, PNG, and WebP images are allowed.");

        return false;
      }

      if (file.size > maxFileSize) {
        setPhotosState(false, "Each photo must not exceed 5 MB.");

        return false;
      }
    }

    setPhotosState(true);

    return true;
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

  if (incidentDateInput) {
    incidentDateInput.addEventListener("input", validateIncidentDate);
    incidentDateInput.addEventListener("change", validateIncidentDate);

    incidentDateInput.addEventListener("invalid", function (event) {
      event.preventDefault();
      validateIncidentDate();
    });
  }

  if (photosInput) {
    photosInput.addEventListener("change", validatePhotos);
  }
  // =====================================
  // Submit Report Form
  // Prevent accidental double submission
  // =====================================

  // =====================================
  // Submit Report Form
  // Check possible duplicates before saving
  // =====================================

  if (reportForm) {
    const duplicateModalElement = document.getElementById(
      "duplicateReportModal",
    );

    const continueDuplicateButton = document.getElementById(
      "continueDuplicateSubmit",
    );

    let duplicateCheckApproved = false;
    let duplicateCheckInProgress = false;

    const originalSubmitHtml = submitButton ? submitButton.innerHTML : "";

    // Restore button after duplicate checking.
    function restoreSubmitButton() {
      if (submitButton) {
        submitButton.disabled = false;
        submitButton.innerHTML = originalSubmitHtml;
      }
    }

    // Continue Submission from the warning modal.
    if (continueDuplicateButton) {
      continueDuplicateButton.addEventListener("click", function () {
        if (isSubmitting || duplicateCheckInProgress) {
          return;
        }

        duplicateCheckApproved = true;

        if (duplicateModalElement && window.bootstrap) {
          const modal = bootstrap.Modal.getInstance(duplicateModalElement);

          if (modal) {
            modal.hide();
          }
        }

        // Re-run validation and submit normally.
        reportForm.requestSubmit();
      });
    }

    reportForm.addEventListener("submit", async function (event) {
      // Prevent accidental double submission.
      if (isSubmitting) {
        event.preventDefault();
        return;
      }

      // Existing form validations.
      const titleValid = validateTitle();
      const categoryValid = validateCategory();
      const descriptionValid = validateDescription();
      const incidentDateValid = validateIncidentDate();
      const locationValid = validateLocation();
      const photosValid = validatePhotos();

      if (
        !titleValid ||
        !categoryValid ||
        !descriptionValid ||
        !incidentDateValid ||
        !locationValid ||
        !photosValid
      ) {
        event.preventDefault();
        return;
      }

      // =====================================
      // Check possible duplicate reports
      // =====================================

      if (!duplicateCheckApproved) {
        event.preventDefault();

        if (duplicateCheckInProgress) {
          return;
        }

        duplicateCheckInProgress = true;

        if (submitButton) {
          submitButton.disabled = true;
          submitButton.textContent = "Checking similar reports...";
        }

        try {
          const checkUrl = reportForm.dataset.duplicateCheckUrl;

          const formData = new FormData();

          // Send report details only, not uploaded photos.
          const fields = [
            "title",
            "category_id",
            "description",
            "incident_date",
            "latitude",
            "longitude",
          ];

          fields.forEach(function (field) {
            const input = reportForm.elements.namedItem(field);

            formData.append(field, input ? input.value : "");
          });

          // Include the existing CSRF token.
          const csrfName = reportForm.dataset.csrfName;

          const csrfInput = Array.from(
            reportForm.querySelectorAll('input[type="hidden"]'),
          ).find(function (input) {
            return input.name === csrfName;
          });

          if (csrfInput) {
            formData.append(csrfName, csrfInput.value);
          }

          const response = await fetch(checkUrl, {
            method: "POST",
            body: formData,
            credentials: "same-origin",
            headers: {
              Accept: "application/json",
            },
          });

          // Update CSRF token for actual submission.
          const newCsrfHash = response.headers.get("X-CSRF-Hash");

          if (newCsrfHash && csrfInput) {
            csrfInput.value = newCsrfHash;
          }

          if (!response.ok) {
            throw new Error("Duplicate check failed.");
          }

          const result = await response.json();

          duplicateCheckInProgress = false;
          restoreSubmitButton();

          if (result.possible_duplicate) {
            // Show the warning modal.
            if (duplicateModalElement && window.bootstrap && bootstrap.Modal) {
              bootstrap.Modal.getOrCreateInstance(duplicateModalElement).show();
            } else {
              // Fallback if Bootstrap modal is unavailable.
              const continueAnyway = window.confirm(
                "A similar report may already exist. " +
                  "Do you want to continue submission?",
              );

              if (continueAnyway) {
                duplicateCheckApproved = true;
                reportForm.requestSubmit();
              }
            }

            return;
          }

          // No possible duplicate: submit normally.
          duplicateCheckApproved = true;
          reportForm.requestSubmit();
        } catch (error) {
          console.error("Duplicate check error:", error);

          duplicateCheckInProgress = false;
          restoreSubmitButton();

          // Never silently skip a failed duplicate check.
          const submitAnyway = window.confirm(
            "Unable to check for similar reports. " +
              "Do you want to submit your report anyway?",
          );

          if (submitAnyway) {
            duplicateCheckApproved = true;
            reportForm.requestSubmit();
          }
        }

        return;
      }

      // =====================================
      // Final normal submission
      // =====================================

      isSubmitting = true;

      if (submitButton) {
        submitButton.disabled = true;

        submitButton.innerHTML =
          '<span class="spinner-border spinner-border-sm me-2" ' +
          'role="status" aria-hidden="true"></span>' +
          "Submitting...";
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
        map.fitBounds(saguingLeafletBounds, {
          padding: [20, 20],
        });
      }

      clearFormFeedback();

      if (locationStatus) {
        locationStatus.textContent =
          "Click the button to use your current location or click on the map to choose a location.";
      }

      setLocationState(false, "Please pin the exact location on the map.");

      if (photoCount) {
        photoCount.textContent = "0 of 5 photos selected";
      }

      setPhotosState(true);
    });
  }
});
