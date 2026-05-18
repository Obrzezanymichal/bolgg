<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);            //raportowanie wszystkich błędów
ini_set('display_errors', 1);

$db = mysqli_connect("localhost", "root", "", "blog");
if (!$db) {
    die("PROBLEM Z POŁĄCZENIEM: " . mysqli_connect_error());
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginOrEmail = trim($_POST['login_or_email'] ?? '');
    $password = $_POST['password'] ?? '';            //hash

    if ($loginOrEmail === '' || $password === '') {
        $message = ' Wypełnij wszystkie pola.';
    } else {
        $stmt = mysqli_prepare($db, "SELECT id, login, haslo FROM uzytkownicy WHERE login = ? OR email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "ss", $loginOrEmail, $loginOrEmail);    
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id, $login, $hash);

        if (mysqli_stmt_fetch($stmt)) {
            if (password_verify($password, $hash)) {           //weryfikaja hasła
                $_SESSION['user'] = $login;
                $message = " Zalogowano pomyślnie jako: " . htmlspecialchars($login, ENT_QUOTES, 'UTF-8');     // serwer wie ze toja 
            } else {
                $message = ' Nieprawidłowy login lub hasło.';
            }
        } else {
            $message = ' Nie znaleziono konta o takim loginie/e-mailu.';
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
        <form action="logowanie.php" method="get">
            <label for="username">Nazwa użytkownika:</label>
            <input type="text" id="username" name="username" required />
    
            <label for="password">Hasło:</label>
            <input type="password" id="password" name="password" required />
    
            <button type="submit">Zaloguj się</button>
    </main>
</body>
</html>
