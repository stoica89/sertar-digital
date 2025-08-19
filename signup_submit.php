<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email  = $_POST['email'];
    $parola = password_hash($_POST['parola'], PASSWORD_DEFAULT);

    // Salvare date (exemplu CSV)
    file_put_contents("users.csv", "$email,$parola\n", FILE_APPEND);

    header("Location: login.html");
    exit;
}
?>
