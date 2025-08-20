<?php
session_start();
$user = $_SESSION['user_email'] ?? '';

$csvFile = __DIR__ . "/documente.csv";
if (file_exists($csvFile)) {
    $rows = array_map('str_getcsv', file($csvFile));
    foreach ($rows as $row) {
        list($owner, $docName, $expiryDate, $fileName) = $row;
        if ($owner === $user) {
            echo "<div class='doc-card'>";
            echo "<h3>" . htmlspecialchars($docName) . "</h3>";
            echo "<p>Expiră la: " . htmlspecialchars($expiryDate) . "</p>";
            echo "<a href='uploads/" . urlencode($fileName) . "' target='_blank'>📂 Vezi document</a>";
            echo "</div>";
        }
    }
}
?>
