// ==========================================
// ADMIN DASHBOARD
// File: js/dashboard.js
// ==========================================

document.addEventListener("DOMContentLoaded", function () {

    console.log("Admin Dashboard Loaded");

    // Dashboard Statistics (Sample Data)
    const stats = {
        totalReports: 245,
        pendingReports: 52,
        progressReports: 76,
        resolvedReports: 117
    };

    // Display Statistics
    const statIds = ["totalReports", "pendingReports", "progressReports", "resolvedReports"];

    statIds.forEach((id) => {
        const element = document.getElementById(id);

        if (element) {
            element.textContent = stats[id];
        }
    });

    // Current Date
    const today = new Date();

    console.log(
        "Today's Date:",
        today.toLocaleDateString("en-US", {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric"
        })
    );

    // Sidebar navigation
    const menuItems = document.querySelectorAll(".menu li");
    const menuLinks = document.querySelectorAll(".menu a");

    menuLinks.forEach((link) => {
        const parentItem = link.closest("li");
        const label = link.textContent.trim();

        if (label === "Reports") {
            link.setAttribute("href", "../report%20A%20.html");
        }

        if (!parentItem || parentItem.classList.contains("logout")) {
            return;
        }

        link.addEventListener("click", function (event) {
            const href = this.getAttribute("href");

            if (!href || href === "#") {
                event.preventDefault();
                alert("This section is coming soon.");
                return;
            }

            menuItems.forEach((item) => item.classList.remove("active"));
            parentItem.classList.add("active");
        });
    });

    const currentPath = window.location.pathname.split("/").pop();
    menuLinks.forEach((link) => {
        const href = link.getAttribute("href") || "";
        const targetPath = href.split("/").pop();
        if (targetPath && currentPath === targetPath) {
            link.closest("li")?.classList.add("active");
        }
    });

    // Card Hover Animation
    const cards = document.querySelectorAll(".dashboard-card");

    cards.forEach((card) => {
        card.addEventListener("mouseenter", () => {
            card.style.transform = "translateY(-8px)";
            card.style.transition = "0.3s";
        });

        card.addEventListener("mouseleave", () => {
            card.style.transform = "translateY(0)";
        });
    });

    // Logout Button
    const logoutBtn = document.querySelector(".logout");

    if (logoutBtn) {
        logoutBtn.addEventListener("click", function (e) {
            e.preventDefault();

            const confirmLogout = confirm("Are you sure you want to logout?");

            if (confirmLogout) {
                alert("Logged out successfully!");
                window.location.href = "login.html";
            }
        });
    }

    // New Announcement Button
    const announceBtn = document.getElementById("newAnnouncementBtn");

    if (announceBtn) {
        announceBtn.addEventListener("click", function (e) {
            e.preventDefault();
            window.location.href = "../announcements.html?open=add";
        });
    }

    // Auto Refresh Dashboard Every 30 Seconds
    setInterval(() => {
        console.log("Refreshing dashboard data...");
    }, 30000);
});