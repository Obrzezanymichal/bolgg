<?php
session_start();

// Włączenie raportowania błędów MySQLi (w PHP 8.1+ jest to już włączone domyślnie)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Połączenie obiektowe (OOP) zamiast proceduralnego
$db = new mysqli("localhost", "root", "", "blog");

if ($db->connect_error) {
    die("PROBLEM Z POŁĄCZENIEM: " . $db->connect_error);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Filtrowanie i pobieranie danych z formularza
    $login    = trim($_POST['new-username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['new-password'] ?? '';
    $repeat   = $_POST['new-re-password'] ?? '';

    // 1. Walidacja: Czy pola są puste?
    if ($login === '' || $email === '' || $password === '' || $repeat === '') {
        $message = 'Wypełnij wszystkie pola formularza.';
    } 
    // 2. Walidacja: Czy hasła się zgadzają?
    elseif ($password !== $repeat) {
        $message = 'Hasła nie są takie same.';
    } 
    // 3. Walidacja: Poprawność formatu e-mail (dodatkowe zabezpieczenie)
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Podany adres e-mail jest nieprawidłowy.';
    } 
    else {
        // Sprawdzenie, czy użytkownik już istnieje
        $stmt = $db->prepare("SELECT id FROM uzytkownicy WHERE login = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $login, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = 'Ten login lub e-mail jest już zajęty.';
            $stmt->close();
        } else {
            $stmt->close(); // Zamykamy poprzednie zapytanie przed wykonaniem nowego

            // Rejestracja nowego użytkownika
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO uzytkownicy (login, haslo, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $login, $hash, $email);

            if ($stmt->execute()) {
                $message = 'Rejestracja zakończona. Możesz się teraz zalogować.';
            } else {
                $message = 'Wystąpił błąd podczas zapisu do bazy danych.';
            }
            $stmt->close();
        }
    }
}
?><!DOCTYPE html>
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
