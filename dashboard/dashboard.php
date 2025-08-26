<?php
// Pornim sesiunea
session_start();

// Anteturi anti-cache pentru a preveni back-button după logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Verificăm autentificarea
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.html");
    exit;
}

// Generăm token CSRF dacă nu există
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// Funcție pentru calculul zilelor până la expirare
function zilePanaLa($date) {
    $today = new DateTime('today');
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d) return null;
    return (int)$today->diff($d)->format('%r%a');
}

// Extragem documentele care expiră curând
$user = $_SESSION['user_email'];
$csvPath = __DIR__ . '/documente.csv';
$curand = [];

if (file_exists($csvPath)) {
    $rows = array_map('str_getcsv', file($csvPath));
    if ($rows && $rows[0][0] === 'user_email') array_shift($rows);

    foreach ($rows as $r) {
        list($owner, $docId, $docName, $expiry, $stored, $orig, $uploadedAt) = $r;
        if ($owner !== $user) continue;

        $zile = zilePanaLa($expiry);
        if ($zile !== null && $zile <= 30) {
            $curand[] = ['id' => $docId, 'name' => $docName, 'zile' => $zile, 'expiry' => $expiry];
        }
    }

    usort($curand, fn($a, $b) => $a['zile'] <=> $b['zile']);
}

// Nume afișat în header
$nume_afisat = '';
if (isset($_SESSION['user_name'])) {
    $nume_afisat = explode(' ', trim($_SESSION['user_name']))[0];
} elseif (isset($_SESSION['user_email'])) {
    $nume_afisat = ucfirst(explode('@', $_SESSION['user_email'])[0]);
}

// Pagina curentă
$current_page = basename($_SERVER['PHP_SELF']);
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

<!-- Sidebar -->
<aside class="sidebar">
    <nav>
        <ul>
            <li><a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">🏠 Dashboard</a></li>
            <li><a href="documente.php" class="<?= $current_page == 'documente.php' ? 'active' : '' ?>">📂 Documente</a></li>
            <li><a href="notificari.php" class="<?= $current_page == 'notificari.php' ? 'active' : '' ?>">🔔 Notificări</a></li>
            <li><a href="setari.php" class="<?= $current_page == 'setari.php' ? 'active' : '' ?>">⚙️ Setări</a></li>
            <li>
                <form action="/sertar-digital.app/sertar-digital/logout.php" method="post" style="margin:0;">
                     <button type="submit" class="logout-btn">🚪 Logout</button>
                </form>
            </li>
        </ul>
    </nav>
</aside>

<!-- Conținut principal -->
<main class="main-content">
    <header class="topbar">
        <h1>Salut <?= htmlspecialchars($nume_afisat) ?> 👋</h1>
        <button class="add-doc" id="btnAddDoc">+ Adaugă document</button>
    </header>

    <?php if (isset($_GET['uploaded'])): ?>
        <div class="flash <?= $_GET['uploaded'] === '1' ? 'success' : 'error' ?>">
            <?= $_GET['uploaded'] === '1' ? 'Documentul a fost adăugat cu succes.' : 'A apărut o eroare la încărcare.' ?>
        </div>
    <?php endif; ?>

    <!-- Documente care expiră curând -->
    <section class="expiring-docs">
  <h2>Documente care expiră curând</h2>
  <div class="cards-container">
    <?php if (!$curand): ?>
      <p>Nu ai documente care expiră în următoarele 30 de zile.</p>
    <?php else: foreach ($curand as $c):
      $cls = $c['zile'] <= 7 ? 'danger' : 'warn'; ?>
      <div class="doc-card <?= $cls ?>">
        <h3><?= htmlspecialchars($c['name']) ?></h3>
        <p><?= $c['zile'] < 0 ? 'Expirat' : 'Expiră în ' . $c['zile'] . ' zile' ?> (<?= htmlspecialchars($c['expiry']) ?>)</p>

        <form action="delete_document.php" method="post" onsubmit="return confirm('Ștergi acest document?');">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
          <input type="hidden" name="doc_id" value="<?= htmlspecialchars($c['id']) ?>">
          <button type="submit" class="btn-delete">🗑️ Șterge</button>
        </form>
      </div>
    <?php endforeach; endif; ?>
  </div>
</section>

<!-- Statistici -->
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

<!-- Modal adăugare document -->
<div id="addDocModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeModal">&times;</span>
        <h2>Adaugă document</h2>
        <form action="upload_document.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

            <label for="docName">Denumire document</label>
            <input type="text" id="docName" name="docName" required>

            <label for="expiryDate">Data expirării</label>
            <input type="date" id="expiryDate" name="expiryDate" required>

            <label for="fileUpload">Fișier</label>
            <input type="file" id="fileUpload" name="fileUpload" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>

            <button type="submit">Salvează</button>
        </form>
    </div>
</div>

<script src="dashboard.js" defer></script>
</body>
</html>

