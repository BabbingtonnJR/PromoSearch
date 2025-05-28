<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'Administrador') {
    header("Location: login.html");
    exit();
}

include "connection.php";

$id = $_SESSION['id_usuario'];

$sql = "SELECT login FROM Usuario WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($login);
    $stmt->fetch();
} else {
    echo "Usuário não encontrado.";
    exit();
}

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Administrador</title>
    <link rel="stylesheet" href="perfil.css">

    <style>
        ul li a{
            font-family: arial;
            text-decoration: none;
            color: white;
        }

        li {
            list-style-type: none;
        }

        a {
            font-family: arial;
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <a href="index_adm.php"><h2>Painel Administrativo</h2></a>
            </div>
            <ul class="nav-links">
                <li class="dropdown">
                    <button class="dropdown-btn">Menu</button>
                    <ul class="dropdown-content">
                        <li><a href="gerenciar_usuarios.php">Gerenciar Usuários</a></li>
                        <li><a href="adicionar_usuario.php">Adicionar Usuário</a></li>
                        <li><a href="visualizar_denuncias.php">Visualizar Denúncias</a></li>
                        <li><a href="historico_penalizacoes.php">Histórico de Penalizações</a></li>
                        <li><a href="logout.php">Sair</a></li>
                    </ul>
                </li>
                <li class="profile">
                    <a href="perfil_adm.php">
                        <img src="https://w7.pngwing.com/pngs/1000/665/png-transparent-computer-icons-profile-s-free-angle-sphere-profile-cliparts-free.png" alt="Perfil Admin">
                    </a>
                </li>
            </ul>
        </div>
    </nav>
<div class="profile-container">
    <div class="profile-header">
        <h1>Meu Perfil</h1>
    </div>
    <div class="profile-content">
        <div class="profile-photo">
            <img src="https://w7.pngwing.com/pngs/1000/665/png-transparent-computer-icons-profile-s-free-angle-sphere-profile-cliparts-free.png" alt="Foto do Perfil">
        </div>
        <div class="profile-details">
            <h2><?php echo $login; ?></h2>
        </div>
    </div>
</div>


</body>
</html>