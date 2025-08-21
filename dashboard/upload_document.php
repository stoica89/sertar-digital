<?php
session_start();

// Protecție acces
if (!isset($_SESSION['user_email'])) {
  header("Location: ../login.html");
  exit;
}

// Verificare metodă
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: dashboard.php");
  exit;
}

// Verificare CSRF
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
  http_response_code(403);
  echo "Invalid CSRF token.";
  exit;
}

$userEmail = $_SESSION['user_email'];
$docName = trim($_POST['docName'] ?? '');
$expiryDate = $_POST['expiryDate'] ?? '';
$file = $_FILES['fileUpload'] ?? null;

if ($docName === '' || $expiryDate === '' || !$file) {
  header("Location: dashboard.php?uploaded=0&err=missing");
  exit;
}

// Validare dată ISO (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiryDate)) {
  header("Location: dashboard.php?uploaded=0&err=date");
  exit;
}

// Config upload
$root = __DIR__;
$uploadsDir = $root . '/uploads';
if (!is_dir($uploadsDir)) {
  mkdir($uploadsDir, 0775, true);
}

// Folder per utilizator
$userHash = substr(hash('sha256', strtolower($userEmail)), 0, 16);
$userDir = $uploadsDir . '/' . $userHash;
if (!is_dir($userDir)) {
  mkdir($userDir, 0775, true);
}

// Validare fișier
$maxSize = 10 * 1024 * 1024; // 10MB
$allowedExt = ['pdf','jpg','jpeg','png','doc','docx'];
$originalName = $file['name'];
$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

if ($file['error'] !== UPLOAD_ERR_OK) {
  header("Location: dashboard.php?uploaded=0&err=upload");
  exit;
}
if ($file['size'] > $maxSize) {
  header("Location: dashboard.php?uploaded=0&err=size");
  exit;
}
if (!in_array($ext, $allowedExt, true)) {
  header("Location: dashboard.php?uploaded=0&err=type");
  exit;
}

// Nume unic pentru stocare
$docId = bin2hex(random_bytes(8));
$storedFile = $docId . '.' . $ext;
$targetPath = $userDir . '/' . $storedFile;

// Mutare fișier
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
  header("Location: dashboard.php?uploaded=0&err=move");
  exit;
}

// Scriere în CSV
$csvPath = $root . '/documente.csv';
$writeHeader = !file_exists($csvPath);

$fp = fopen($csvPath, 'a');
if ($fp === false) {
  header("Location: dashboard.php?uploaded=0&err=csv");
  exit;
}

if ($writeHeader) {
  fputcsv($fp, ['user_email','doc_id','doc_name','expiry_date','stored_path','original_name','uploaded_at']);
}

$storedRel = 'uploads/' . $userHash . '/' . $storedFile; // cale relativă pentru link
$uploadedAt = date('Y-m-d H:i:s');
fputcsv($fp, [$userEmail, $docId, $docName, $expiryDate, $storedRel, $originalName, $uploadedAt]);
fclose($fp);

// Succes
header("Location: dashboard.php?uploaded=1");
exit;

