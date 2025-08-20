<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $prenume = trim($_POST['prenume']);
    $nume    = trim($_POST['nume']);
    $email   = $_POST['email'];
    $parola  = password_hash($_POST['parola'], PASSWORD_DEFAULT);

    // Salvăm: prenume, nume, email, parolă hash
    file_put_contents("users.csv", "$prenume,$nume,$email,$parola\n", FILE_APPEND);

    header("Location: login.html");
    exit;
}
?>
