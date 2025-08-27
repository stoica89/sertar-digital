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

// Citire documente din CSV
if (file_exists($csvPath)) {
    $rows = array_map('str_getcsv', file($csvPath));
    if ($rows && $rows[0][0] === 'user_email') array_shift($rows);
    foreach ($rows as $r) {
        // user_email, doc_id, doc_name, expiry, stored, orig, uploaded_at
        list($owner, $docId, $docName, $expiry, $stored, $orig, $uploadedAt) = $r;
        if ($owner !== $user) continue;
        $docs[] = [
            'doc_id' => $docId,
            'doc_name' => $docName,
            'expiry' => $expiry,
            'stored' => $stored
        ];
    }
}
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

    <!-- Butonul și modalul -->
    <button class="add-doc" id="btnAddDoc">+ Adaugă document</button>

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

  <script src="dashboard.js" defer></script>
</body>
</html>
