<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email  = $_POST['email'];
    $parola = $_POST['parola'];

    $users = file("users.csv", FILE_IGNORE_NEW_LINES);

    foreach ($users as $user) {
        list($storedPrenume, $storedNume, $storedEmail, $storedPass) = explode(",", $user);

        if ($storedEmail === $email && password_verify($parola, $storedPass)) {
            session_regenerate_id(true);

            // Salvăm în sesiune prenumele și email-ul
            $_SESSION['user_email'] = $storedEmail;
            $_SESSION['user_name']  = $storedPrenume;

            header("Location: dashboard/dashboard.php");
            exit;
        }
    }

    echo "E-mail sau parolă greșite.";
}
?>
