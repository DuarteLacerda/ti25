document.addEventListener("DOMContentLoaded", () => {
  const toggleButton = document.getElementById("sidebarToggle");
  const sidebar = document.getElementById("sidebar");
  const toggles = document.querySelectorAll('[data-bs-toggle="collapse"]');
  const sidebarToggle = document.getElementById("sidebarToggle");

  toggleButton.addEventListener("click", () => {
    sidebar.classList.toggle("show");
  });

  // Gira os ícones nas gavetas
  toggles.forEach((toggle) => {
    const targetId = toggle.getAttribute("href");
    const collapseEl = document.querySelector(targetId);
    const icon = toggle.querySelector(".icon-collapse");

    if (collapseEl && icon) {
      collapseEl.addEventListener("show.bs.collapse", () =>
        icon.classList.add("rotate")
      );
      collapseEl.addEventListener("hide.bs.collapse", () =>
        icon.classList.remove("rotate")
      );
    }
  });

  // Sidebar toggle mobile
  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", () => {
      sidebar.classList.toggle("active");
    });
  }
  // Close sidebar when clicking outside
  document.addEventListener("click", (event) => {
    if (
      !sidebar.contains(event.target) &&
      !toggleButton.contains(event.target)
    ) {
      sidebar.classList.remove("show");
      sidebar.classList.remove("active");
    }
  });
});
