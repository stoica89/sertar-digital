document.addEventListener("DOMContentLoaded", () => {
  // Preluăm datele din sessionStorage
  const nume = sessionStorage.getItem("user_name") || "";
  const email = sessionStorage.getItem("user_email") || "";

  document.getElementById("nume").value = nume;
  document.getElementById("email").value = email;
});

document
  .getElementById("settingsForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();
    const nume = document.getElementById("nume").value.trim();
    const email = document.getElementById("email").value.trim();
    const parola = document.getElementById("parola").value;

    const msg = document.getElementById("settingsMsg");
    msg.textContent = "";
    msg.className = "";

    if (!nume || !email) {
      msg.textContent = "Completează toate câmpurile obligatorii!";
      msg.className = "settings-error";
      return;
    }
    // Actualizează datele în sessionStorage (frontend)
    sessionStorage.setItem("user_name", nume);
    sessionStorage.setItem("user_email", email);

    msg.textContent = "Setările au fost salvate!";
    msg.className = "settings-success";
    setTimeout(() => {
      msg.textContent = "";
    }, 2500);
  });
