document.addEventListener("DOMContentLoaded", () => {
  /* ========================
     Modal "Adaugă document"
  ======================== */
  const modal = document.getElementById("addDocModal");
  const btnAdd = document.getElementById("btnAddDoc");
  const btnClose = document.getElementById("closeModal");

  if (modal) {
    const openModal = () => modal.classList.add("active");
    const closeModal = () => modal.classList.remove("active");

    if (btnAdd) btnAdd.addEventListener("click", openModal);
    if (btnClose) btnClose.addEventListener("click", closeModal);

    // Click în afara ferestrei
    window.addEventListener("click", (e) => {
      if (e.target === modal) closeModal();
    });

    // Escape
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && modal.classList.contains("active")) {
        closeModal();
      }
    });
  }
});
