<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'Administrador') {
    header("Location: login.html");
    exit();
}

include "connection.php";

$sql = "SELECT COUNT(*) FROM Usuario";
$totalUsuarios = $conn->query($sql)->fetch_row()[0];

$sql = "SELECT COUNT(*) FROM Loja";
$totalLojas = $conn->query($sql)->fetch_row()[0];

$sql = "SELECT COUNT(*) FROM Denuncia WHERE estado = 0"; 
$denunciasPendentes = $conn->query($sql)->fetch_row()[0];

$sql = "SELECT COUNT(*) FROM Registro";
$penalizacoesTotal = $conn->query($sql)->fetch_row()[0];
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador - Página Inicial</title>
    <link rel="stylesheet" href="styles_adm.css">

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

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 30px;
        }

        .card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            margin-bottom: 10px;
            font-size: 1.1rem;
            color: #333;
        }

        .card p {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 30px 0;
        }

        .quick-action-card {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 120px;
        }

        .quick-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
            background-color: #f8f9fa;
        }

        .quick-action-card .icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: block;
        }

        .quick-action-card .label {
            font-size: 1.1rem;
            font-weight: 500;
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

    <div class="content">
        <h1>Painel Administrativo</h1>
        <p>Bem-vindo, administrador. Use o menu acima para navegar entre as opções administrativas.</p>
        <br><br>

        <div class="quick-actions">
            <a href="adicionar_usuario.php" class="quick-action-card">
                <span class="icon">+</span>
                <span class="label">Adicionar novo usuário</span>
            </a>
            <a href="visualizar_denuncias.php" class="quick-action-card">
                <span class="icon">🕵️</span>
                <span class="label">Visualizar denúncias pendentes</span>
            </a>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h3>Total de Usuários</h3>
                <p><?php echo $totalUsuarios; ?></p>
            </div>
            <div class="card">
                <h3>Total de Lojas</h3>
                <p><?php echo $totalLojas; ?></p>
            </div>
            <div class="card">
                <h3>Denúncias Pendentes</h3>
                <p><?php echo $denunciasPendentes; ?></p>
            </div>
            <div class="card">
                <h3>Penalizações Aplicadas</h3>
                <p><?php echo $penalizacoesTotal; ?></p>
            </div>
        </div>

    </div>
</body>
</html>
