<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

include "connection.php";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <h2>PromoSearch</h2>
            </div>
            <ul class="nav-links">
                <li class="dropdown">
                    <button class="dropdown-btn">Menu</button>
                    <ul class="dropdown-content">
                        <li><a href="#">Mapa</a></li>
                        <li><a href="#">Página 2</a></li>
                        <li><a href="#">Página 3</a></li>
                    </ul>
                </li>
                <li class="profile">
                    <a href="perfil.php">
                        <img src="https://w7.pngwing.com/pngs/1000/665/png-transparent-computer-icons-profile-s-free-angle-sphere-profile-cliparts-free.png" alt="Perfil">
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="content">
        <h1>Mapa</h1>
    </div>
</body>
</html>
