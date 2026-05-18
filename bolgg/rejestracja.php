<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Konfiguracja połączenia PDO
$host    = 'localhost';
$dbName  = 'blog';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Włącza rzucanie wyjątków przy błędach
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Tablice asocjacyjne jako domyślny format danych
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Wyłączenie emulacji dla lepszego bezpieczeństwa
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("PROBLEM Z POŁĄCZENIEM: " . $e->getMessage());
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['new-username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['new-password'] ?? '';
    $repeat   = $_POST['new-re-password'] ?? '';

    // Wczesne powracanie (Early Return) - sprawdzamy błędy najpierw
    if ($login === '' || $email === '' || $password === '' || $repeat === '') {
        $message = 'Wypełnij wszystkie pola formularza.';
    } elseif ($password !== $repeat) {
        $message = 'Hasła nie są takie same.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Podany adres e-mail jest nieprawidłowy.';
    } else {
        try {
            // 1. Sprawdzenie czy użytkownik istnieje (używamy nazwanych parametrów :login, :email)
            $stmt = $pdo->prepare("SELECT id FROM uzytkownicy WHERE login = :login OR email = :email LIMIT 1");
            $stmt->execute(['login' => $login, 'email' => $email]);
            
            if ($stmt->fetch()) {
                $message = 'Ten login lub e-mail jest już zajęty.';
            } else {
                // 2. Rejestracja nowego użytkownika
                $hash = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO uzytkownicy (login, haslo, email) VALUES (:login, :haslo, :email)");
                $success = $stmt->execute([
                    'login' => $login,
                    'haslo' => $hash,
                    'email' => $email
                ]);

                if ($success) {
                    $message = 'Rejestracja zakończona. Możesz się teraz zalogować.';
                } else {
                    $message = 'Wystąpił błąd podczas zapisu do bazy danych.';
                }
            }
        } catch (\PDOException $e) {
            // Logowanie błędu dla programisty, ogólny komunikat dla użytkownika
            error_log($e->getMessage());
            $message = 'Wystąpił nieoczekiwany błąd serwera.';
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
