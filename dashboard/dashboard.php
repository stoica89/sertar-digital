<?php
session_start();

// Anteturi anti-cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Protecție acces
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sertar.digital - Dashboard</title>
  <link rel="stylesheet" href="dashboard.css" />
</head>
<body>
  <!-- Sidebar navigație -->
  <aside class="sidebar">
    <div class="logo">Sertar.digital</div>
    <nav>
      <ul>
        <li><a href="#">Dashboard</a></li>
        <li><a href="#">Documente</a></li>
        <li><a href="#">Notificări</a></li>
        <li><a href="#">Setări</a></li>
        <li><button id="btnLogout">Logout</button></li>
      </ul>
    </nav>
  </aside>

  <!-- Conținut principal -->
  <main class="main-content">
    <header class="topbar">
      <h1>Salut, Stoica 👋</h1>
      <button class="add-doc">+ Adaugă document</button>
    </header>

    <!-- Secțiune carduri documente -->
    <section class="expiring-docs">
      <h2>Documente care expiră curând</h2>
      <div class="cards-container">
        <!-- Carduri individuale -->
        <div class="doc-card">
          <h3>CI - Stoica</h3>
          <p>Expiră în 5 zile</p>
        </div>
        <div class="doc-card">
          <h3>Pașaport</h3>
          <p>Expiră în 12 zile</p>
        </div>
      </div>
    </section>

    <!-- Secțiune statistici -->
    <section class="stats">
      <h2>Statistici rapide</h2>
      <div class="stats-grid">
        <div class="stat-box">
          <h3>12</h3>
          <p>Documente active</p>
        </div>
        <div class="stat-box">
          <h3>3</h3>
          <p>Notificări trimise</p>
        </div>
      </div>
    </section>
  </main>
  <script src="dashboard.js"></script>
</body>
</html>
