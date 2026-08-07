/*
=========================================
Profile
Community Problems Visibility System
=========================================
*/

document.addEventListener("DOMContentLoaded", function () {

    console.log("Profile Page Loaded");

    const profileForm = document.getElementById("profileForm");

    profileForm.addEventListener("submit", function (event) {

        event.preventDefault();

        const currentPassword = document.getElementById("currentPassword").value.trim();

        const newPassword = document.getElementById("newPassword").value.trim();

        const confirmPassword = document.getElementById("confirmPassword").value.trim();

        // =====================================
        // Password Validation
        // =====================================

        if (newPassword !== "" || confirmPassword !== "") {

            if (currentPassword === "") {

                alert("Please enter your current password.");

                return;

            }

            if (newPassword.length < 8) {

                alert("New password must be at least 8 characters.");

                return;

            }

            if (newPassword !== confirmPassword) {

                alert("New password and confirmation password do not match.");

                return;

            }

        }

        alert("Profile updated successfully!\n\n(Frontend demo only. Backend integration will be added later.)");

    });

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

    barangay: "Barangay Saguing"

};

console.table(resident);