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