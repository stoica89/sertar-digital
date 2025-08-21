<?php
// delete_document.php
session_start();

// Acces doar pentru utilizatori autentificați
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.html");
    exit;
}

// Doar prin POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: documente.php?deleted=0&err=method");
    exit;
}

// CSRF
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    http_response_code(403);
    echo "Invalid CSRF token.";
    exit;
}

// Parametri
$docId = trim($_POST['doc_id'] ?? '');
if ($docId === '') {
    header("Location: documente.php?deleted=0&err=param");
    exit;
}

$userEmail = $_SESSION['user_email'];
$csvPath   = __DIR__ . '/documente.csv';
$uploadsBase = realpath(__DIR__ . '/uploads'); // baza uploads pentru verificare

if (!file_exists($csvPath)) {
    header("Location: documente.php?deleted=0&err=nocs");
    exit;
}

// Citim CSV și identificăm rândul
$rows = array_map('str_getcsv', file($csvPath));
$hasHeader = $rows && $rows[0] && $rows[0][0] === 'user_email';
if ($hasHeader) array_shift($rows);

$kept = [];
$deletedRow = null;

foreach ($rows as $r) {
    // Așteptăm 7 coloane: user_email, doc_id, doc_name, expiry_date, stored_path, original_name, uploaded_at
    if (count($r) < 7) { 
        $kept[] = $r; 
        continue; 
    }
    list($owner, $rid, $docName, $expiry, $storedRel, $orig, $uploadedAt) = $r;

    if ($owner === $userEmail && hash_equals($rid, $docId)) {
        $deletedRow = $r; // marcat pentru ștergere
        continue;         // nu îl mai păstrăm
    }
    $kept[] = $r;
}

if ($deletedRow === null) {
    header("Location: documente.php?deleted=0&err=notfound");
    exit;
}

// Rescriem CSV atomic (cu header)
$temp = $csvPath . '.tmp';
$fp = fopen($temp, 'w');
if ($fp === false) {
    header("Location: documente.php?deleted=0&err=csvopen");
    exit;
}
flock($fp, LOCK_EX);
fputcsv($fp, ['user_email','doc_id','doc_name','expiry_date','stored_path','original_name','uploaded_at']);
foreach ($kept as $row) {
    fputcsv($fp, $row);
}
flock($fp, LOCK_UN);
fclose($fp);

// Înlocuim fișierul original
if (!rename($temp, $csvPath)) {
    header("Location: documente.php?deleted=0&err=rename");
    exit;
}

// Ștergem fișierul din disc în siguranță
$storedRel = $deletedRow[4]; // uploads/<userHash>/<file>
$fullPath = realpath(__DIR__ . DIRECTORY_SEPARATOR . $storedRel);

// Verificăm că fișierul este în uploads/ (protecție path traversal)
if ($fullPath !== false && $uploadsBase !== false) {
    $uploadsBaseNormalized = rtrim($uploadsBase, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    $isInsideUploads = strpos($fullPath, $uploadsBaseNormalized) === 0;
    if ($isInsideUploads && file_exists($fullPath)) {
        @unlink($fullPath);
    }
}

// Redirect cu succes
header("Location: documente.php?deleted=1");
exit;
