<?php
session_start();
if (!isset($_SESSION['user_email'])) {
  header("Location: ../login.html");
  exit;
}
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

$user = $_SESSION['user_email'];
$csvPath = __DIR__ . '/documente.csv';
$docs = [];

// ... codul tău de citire CSV, populare $docs (inclusiv doc_id) ...

?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <title>Documente</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <main class="main-content">
    <h1>Documentele mele</h1>

    <?php if (isset($_GET['deleted'])): ?>
      <div class="flash <?= $_GET['deleted'] === '1' ? 'success' : 'error' ?>">
        <?= $_GET['deleted'] === '1' ? 'Documentul a fost șters.' : 'Nu s-a putut șterge documentul.' ?>
      </div>
    <?php endif; ?>

    <div class="cards-container">
      <?php if (!$docs): ?>
        <p>Nu ai încă documente încărcate.</p>
      <?php else: foreach ($docs as $d): ?>
        <div class="doc-card">
          <h3><?= htmlspecialchars($d['doc_name']) ?></h3>
          <p>Expiră la: <?= htmlspecialchars($d['expiry']) ?></p>
          <div class="doc-actions">
            <a href="<?= htmlspecialchars($d['stored']) ?>" target="_blank" class="btn">📂 Deschide</a>

            <form action="delete_document.php" method="post" onsubmit="return confirm('Sigur ștergi acest document?');" style="display:inline;">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
              <input type="hidden" name="doc_id" value="<?= htmlspecialchars($d['doc_id']) ?>">
              <button type="submit" class="btn-delete">🗑️ Șterge</button>
            </form>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </main>
</body>
</html>
