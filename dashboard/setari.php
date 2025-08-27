
<!-- filepath: c:\xampp\htdocs\sertar-digital.app\sertar-digital\dashboard\setari.php -->
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sertar.digital - Setări</title>
  <link rel="stylesheet" href="dashboard.css" />
  <link rel="stylesheet" href="setari.css" />
</head>
<body>
  <main class="main-content">
    <a href="dashboard.php" class="back-btn">&larr; Înapoi la Dashboard</a>
    <div class="settings-container">
      <h2>Setările contului</h2>
      <form class="settings-form" id="settingsForm" autocomplete="off">
        <label for="nume">Nume</label>
        <input type="text" id="nume" name="nume" placeholder="Numele tău" required />

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Email-ul tău" required />

        <label for="parola">Parolă nouă</label>
        <input type="password" id="parola" name="parola" placeholder="Lasă gol dacă nu vrei să schimbi" />

        <button type="submit">Salvează modificările</button>
        <div id="settingsMsg"></div>
      </form>
    </div>
  </main>
  <script src="setari.js"></script>
</body>
</html>