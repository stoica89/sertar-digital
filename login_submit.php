<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email  = $_POST['email'];
    $parola = $_POST['parola'];

    $users = file("users.csv", FILE_IGNORE_NEW_LINES);

    foreach ($users as $user) {
        list($storedEmail, $storedPass) = explode(",", $user);
        if ($storedEmail === $email && password_verify($parola, $storedPass)) {
            $_SESSION['user_email'] = $email;
            header("Location: dashboard/dashboard.php");
            exit;
        }
    }

    echo "E-mail sau parolă greșite.";
}
?>
