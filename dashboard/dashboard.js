document.addEventListener("DOMContentLoaded", function () {
  /* ========================
     Buton Logout
  ======================== */
  const btnLogout = document.querySelector(".logout-btn");
  if (btnLogout) {
    btnLogout.addEventListener("click", function (e) {
      // Dacă vrei POST, lasă formularul să se trimită nativ și elimină acest JS
      e.preventDefault();
      window.location.href = "../logout.php"; 
    });
  }

  /* ========================
     Modal "Adaugă document"
  ======================== */
  const modal = document.getElementById("addDocModal");
  const btnAdd = document.getElementById("btnAddDoc");
  const btnClose = document.getElementById("closeModal");

  // Deschidere modal
  if (btnAdd) {
    btnAdd.addEventListener("click", () => {
      modal.style.display = "block";
    });
  }

  // Închidere cu "X"
  if (btnClose) {
    btnClose.addEventListener("click", () => {
      modal.style.display = "none";
    });
  }

  // Închidere la click în afara ferestrei
  window.addEventListener("click", (event) => {
    if (event.target === modal) {
      modal.style.display = "none";
    }
  });

  // Închidere cu tasta Escape
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modal.style.display === "block") {
      modal.style.display = "none";
    }
  });
});



