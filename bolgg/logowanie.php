<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Konfiguracja połączenia PDO (najlepiej przenieść to do osobnego pliku, np. config.php)
$host    = 'localhost';
$dbName  = 'blog';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("PROBLEM Z POŁĄCZENIEM: " . $e->getMessage());
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginOrEmail = trim($_POST['login_or_email'] ?? '');
    $password     = $_POST['password'] ?? '';

    // 1. Walidacja: Czy pola nie są puste?
    if ($loginOrEmail === '' || $password === '') {
        $message = 'Wypełnij wszystkie pola.';
    } else {
        try {
            // 2. Pobranie danych użytkownika z bazy danych
            $stmt = $pdo->prepare("SELECT id, login, haslo FROM uzytkownicy WHERE login = :input OR email = :input LIMIT 1");
            $stmt->execute(['input' => $loginOrEmail]);
            $user = $stmt->fetch(); // Zwraca tablicę asocjacyjną lub false

            // 3. Weryfikacja: Jeśli użytkownik istnieje, sprawdzamy hasło
            // Uwaga: Bezpieczniej jest dać ten sam komunikat ("Nieprawidłowy login lub hasło") 
            // zarówno gdy hasło jest złe, jak i gdy login nie istnieje.
            if ($user && password_verify($password, $user['haslo'])) {
                // Regeneracja ID sesji chroni przed atakami typu Session Fixation
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user']    = $user['login'];
                
                $message = "Zalogowano pomyślnie jako: " . htmlspecialchars($user['login'], ENT_QUOTES, 'UTF-8');
            } else {
                $message = 'Nieprawidłowy login lub hasło.';
            }
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            $message = 'Wystąpił nieoczekiwany błąd serwera.';
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
