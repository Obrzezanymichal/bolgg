<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db = mysqli_connect("localhost", "root", "", "blog");
if (!$db) {
    die("PROBLEM Z POŁĄCZENIEM: " . mysqli_connect_error());
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['new-username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['new-password'] ?? '';
    $repeat = $_POST['new-re-password'] ?? '';

    if ($login === '' || $email === '' || $password === '' || $repeat === '') {
        $message = ' Wypełnij wszystkie pola formularza.';
    } elseif ($password !== $repeat) {
        $message = ' Hasła nie są takie same.';
    } else {
        $stmt = mysqli_prepare($db, "SELECT id FROM uzytkownicy WHERE login = ? OR email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "ss", $login, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $message = ' Ten login lub e-mail jest już zajęty.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($db, "INSERT INTO uzytkownicy (login, haslo, email) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $login, $hash, $email);

            if (mysqli_stmt_execute($stmt)) {
                $message = ' Rejestracja zakończona. Możesz się teraz zalogować.';
            } else {
                $message = ' Wystąpił błąd podczas zapisu do bazy danych.';
            }
        }
    }
}
?>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
    <a href="index.html">Blogg</a>
    <a href="rejestracja.php">Rejestracja</a>
    <a href="logowanie.php">Logowanie</a>
    </header>
    <main>
        <form action="rejestracja.php" method="get">
            <label for="new-username">Nazwa użytkownika:</label>
            <input type="text" id="new-username" name="new-username" required />
    
            <label for="new-password">Hasło:</label>
            <input type="password" id="new-password" name="new-password" required />
    
            <label for="new-re-password">Powtórz hasło:</label>
            <input type="password" id="new-re-password" name="new-re-password" required />

            <button type="submit">Zarejestruj się</button>
            </form> 
            <p>Masz już konto? <a href="logowanie.php" id="showLogin">Zaloguj się</a></p>
    </main>
</body>
</html>
