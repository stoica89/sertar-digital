document.querySelector("form").addEventListener("submit", function (e) {
  const nume = document.querySelector('input[name="nume"]').value.trim();
  const email = document.querySelector('input[name="email"]').value.trim();
  const tip = document.querySelector('select[name="tip_utilizator"]').value;
  const termeni = document.querySelector('input[name="termeni"]').checked;

  if (!nume || !email || !tip || !termeni) {
    e.preventDefault();
    alert("Completează toate câmpurile și acceptă termenii.");
  }
});
