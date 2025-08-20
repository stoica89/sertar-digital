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
<?php
  $current_page = basename($_SERVER['PHP_SELF']);
?>

  <!-- Sidebar -->
<aside class="sidebar">
  <nav>
    <ul>
      <li><a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">🏠 Dashboard</a></li>
      <li><a href="documente.php" class="<?= $current_page == 'documente.php' ? 'active' : '' ?>">📂 Documente</a></li>
      <li><a href="notificari.php" class="<?= $current_page == 'notificari.php' ? 'active' : '' ?>">🔔 Notificări</a></li>
      <li><a href="setari.php" class="<?= $current_page == 'setari.php' ? 'active' : '' ?>">⚙️ Setări</a></li>
      <li>
        <form action="logout.php" method="post" style="margin:0;">
          <button type="submit" class="logout-btn">🚪 Logout</button>
        </form>
      </li>
    </ul>
  </nav>
</aside>

  <!-- Conținut principal -->
  <main class="main-content">
    <header class="topbar">
      <?php
    // Extragem doar primul nume
    $nume_afisat = '';

    if (isset($_SESSION['user_name'])) {
        // Dacă am numele complet salvat, luăm doar primul cuvânt
        $nume_afisat = explode(' ', trim($_SESSION['user_name']))[0];
    } elseif (isset($_SESSION['user_email'])) {
        // Dacă avem doar email-ul, luăm partea dinainte de @
        $nume_afisat = ucfirst(explode('@', $_SESSION['user_email'])[0]);
    }
?>
  <h1>Salut <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</h1>
  <button class="add-doc" id="btnAddDoc">+ Adaugă document</button>
  </header>

    <!-- Secțiune carduri documente -->
    <section class="expiring-docs">
      <h2>Documente care expiră curând</h2>
      <div class="cards-container">
        <!-- Carduri individuale -->
        <div class="doc-card">
          <h3>CI </h3>
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
  
  <!-- Formular modal pentru adăugare document -->
<div id="addDocModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" id="closeModal">&times;</span>
    <h2>Adaugă document</h2>
    <form action="upload_document.php" method="POST" enctype="multipart/form-data">
      <label for="docName">Denumire document</label>
      <input type="text" id="docName" name="docName" required>

      <label for="expiryDate">Data expirării</label>
      <input type="date" id="expiryDate" name="expiryDate" required>

      <label for="fileUpload">Fișier</label>
      <input type="file" id="fileUpload" name="fileUpload" required>

      <button type="submit">Salvează</button>
    </form>
  </div>
</div>

<script src="dashboard.js" defer></script>
</body>
</html>
