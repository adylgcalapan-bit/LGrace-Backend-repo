// ==========================================
// SHARED ADMIN RESPONSIVE SIDEBAR
// ==========================================

document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.querySelector(".sidebar");

  const toggleButton = document.querySelector(".admin-mobile-toggle");

  const overlay = document.querySelector(".admin-sidebar-overlay");

  if (!sidebar || !toggleButton || !overlay) {
    return;
  }

  const openSidebar = () => {
    sidebar.classList.add("admin-sidebar-open");

    overlay.classList.add("show");

    toggleButton.setAttribute("aria-expanded", "true");

    document.body.classList.add("admin-menu-open");
  };

  const closeSidebar = () => {
    sidebar.classList.remove("admin-sidebar-open");

    overlay.classList.remove("show");

    toggleButton.setAttribute("aria-expanded", "false");

    document.body.classList.remove("admin-menu-open");
  };

  const toggleSidebar = (event) => {
    event.preventDefault();
    event.stopPropagation();

    if (sidebar.classList.contains("admin-sidebar-open")) {
      closeSidebar();
    } else {
      openSidebar();
    }
  };

  // Hamburger
  toggleButton.addEventListener("click", toggleSidebar);

  // Click outside sidebar
  overlay.addEventListener("click", closeSidebar);

  // Close after selecting sidebar link
  sidebar.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      if (window.innerWidth <= 768) {
        closeSidebar();
      }
    });
  });

  // Escape key
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeSidebar();
    }
  });

  // Return to desktop layout
  window.addEventListener("resize", () => {
    if (window.innerWidth > 768) {
      closeSidebar();
    }
  });
});
