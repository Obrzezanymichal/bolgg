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