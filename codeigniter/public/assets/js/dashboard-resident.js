/*
=========================================
Resident Dashboard
Community Problems Visibility System
=========================================
*/

document.addEventListener("DOMContentLoaded", function () {
  console.log("Resident Dashboard Loaded");

  // Highlight active sidebar menu
  const menuItems = document.querySelectorAll(".menu li");

  menuItems.forEach((item) => {
    item.addEventListener("click", function () {
      menuItems.forEach((menu) => menu.classList.remove("active"));

      this.classList.add("active");
    });
  });

  // Welcome message
  const residentName = "Juan Dela Cruz";

  console.log("Welcome " + residentName);
});

/*
=========================================
View Button
=========================================
*/

/*
=========================================
Report Button
=========================================
*/

const reportButton = document.querySelector(".welcome-banner .btn");

if (reportButton) {
  reportButton.addEventListener("click", function () {
    console.log("Redirecting to Report Page...");
  });
}

/*
=========================================
Dashboard Summary
(Currently Static)
=========================================
*/

const dashboardSummary = {
  pending: 2,
  inProgress: 1,
  resolved: 5,
  total: 8,
};

console.table(dashboardSummary);

/*
=========================================
Future Backend Functions
=========================================

These will be connected to CodeIgniter later.

Examples:

loadResidentInfo();

loadRecentReports();

loadNotifications();

updateDashboardCards();

fetchReports();

=========================================
*/
