/*
=========================================
Profile
Community Problems Visibility System
=========================================
*/

document.addEventListener("DOMContentLoaded", function () {
  console.log("Profile Page Loaded");

  // =====================================
  // Reset Form
  // =====================================

  profileForm.addEventListener("reset", function () {
    setTimeout(function () {
      alert("Changes have been cancelled.");
    }, 100);
  });
});

/*
=========================================
Future Backend Functions
=========================================

These functions will be connected
to CodeIgniter later.

Examples:

loadProfile();

updateProfile();

changePassword();

uploadProfilePicture();

=========================================
*/

/*
=========================================
Demo Resident Data
=========================================
*/

const resident = {
  fullName: "Juan Dela Cruz",

  email: "juan@email.com",

  contactNumber: "09123456789",

  barangay: "Barangay Saguing",
};

console.table(resident);
