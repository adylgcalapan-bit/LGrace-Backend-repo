// ========================================
// REPORT DETAILS JAVASCRIPT
// Community Visibility System - Resident UI
// ========================================


// ================================
// SAMPLE REPORT DATA
// (Temporary data for frontend testing)
// This will be replaced by CodeIgniter data
// ================================

const reportData = {
    id: "RPT-001",
    title: "Garbage Overflow Near Road",
    category: "Waste Management",
    status: "Pending",
    date: "July 28, 2026",
    location: "Barangay Saguing, Kidapawan City",
    latitude: 7.0089,
    longitude: 125.0897,
    description:
        "Large amount of garbage has accumulated near the roadside causing bad smell and possible health concerns.",
    resident:
        "Anonymous Resident"
};


// ================================
// DISPLAY REPORT INFORMATION
// ================================

document.addEventListener("DOMContentLoaded", function () {

    loadReportDetails();

    initializeMap();

    setupPrintButton();

    setupBackButton();

});


// ================================
// LOAD REPORT DETAILS
// ================================

function loadReportDetails() {

    const setValue = (id, value) => {
        const element = document.getElementById(id);

        if (!element) {
            return;
        }

        if (element.tagName === "INPUT" || element.tagName === "TEXTAREA") {
            element.value = value;
        } else {
            element.textContent = value;
        }
    };

    setValue("report-id", reportData.id);
    setValue("report-title", reportData.title);
    setValue("report-category", reportData.category);
    setValue("report-status", reportData.status);
    setValue("report-date", reportData.date);
    setValue("report-location", reportData.location);
    setValue("report-description", reportData.description);
    setValue("report-resident", reportData.resident);

}


// ================================
// LEAFLET MAP INITIALIZATION
// ================================

function initializeMap() {

    const mapContainer = document.getElementById("report-map");

    if (!mapContainer || typeof L === "undefined") {
        return;
    }

    // Create map
    const map = L.map(mapContainer).setView(
        [
            reportData.latitude,
            reportData.longitude
        ],
        16
    );


    // OpenStreetMap Layer
    L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
            attribution:
                '&copy; OpenStreetMap contributors'
        }
    ).addTo(map);



    // Report Marker
    const marker = L.marker(
        [
            reportData.latitude,
            reportData.longitude
        ]
    ).addTo(map);



    // Popup Information
    marker.bindPopup(
        `
        <div>
            <h4>${reportData.title}</h4>

            <p>
            <b>Category:</b>
            ${reportData.category}
            </p>

            <p>
            <b>Status:</b>
            ${reportData.status}
            </p>

        </div>
        `
    ).openPopup();


}


// ================================
// PRINT REPORT
// ================================

function setupPrintButton(){

    const printButton =
        document.getElementById("print-report");


    if(printButton){

        printButton.addEventListener(
            "click",
            function(){

                window.print();

            }
        );

    }

}



// ================================
// BACK BUTTON
// ================================

function setupBackButton(){

    const backButton =
        document.getElementById("back-button");


    if(backButton){

        backButton.addEventListener(
            "click",
            function(){

                window.history.back();

            }
        );

    }

}



// ================================
// FUTURE CODEIGNITER API READY
// ================================


// Example future usage:
//
// fetch('/resident/report-details/'+id)
// .then(response => response.json())
// .then(data => {
//      reportData = data;
//      loadReportDetails();
//      initializeMap();
// });