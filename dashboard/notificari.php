<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.html");
    exit;
}
$user = $_SESSION['user_email'];
$notificariPath = __DIR__ . '/notificari.csv';
$notificari = [];

if (file_exists($notificariPath)) {
    $rows = array_map('str_getcsv', file($notificariPath));
    if ($rows && $rows[0][0] === 'user_email') array_shift($rows);
    foreach ($rows as $r) {
        // user_email, doc_id, mesaj, data
        if ($r[0] === $user) {
            $notificari[] = [
                'doc_id' => $r[1],
                'mesaj' => $r[2],
                'data' => $r[3]
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Notificările mele</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<main class="main-content">
    <h1>Notificările mele</h1>
    <a href="dashboard.php" class="btn" style="margin-bottom:20px;display:inline-block;">&larr; Înapoi la Dashboard</a>
    <div id="notificari-list" class="cards-container">
        <?php if (!$notificari): ?>
            <p>Nu ai notificări.</p>
        <?php else: foreach ($notificari as $n): ?>
            <div class="doc-card">
                <p><?= htmlspecialchars($n['mesaj']) ?></p>
                <small><?= htmlspecialchars($n['data']) ?></small>
            </div>
        <?php endforeach; endif; ?>
    </div>
</main>
<script src="notificari.js" defer></script>
</body>
</html>