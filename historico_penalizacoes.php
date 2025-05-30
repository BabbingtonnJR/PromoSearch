<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'Administrador') {
    header("Location: login.html");
    exit();
}

include "connection.php";

$sql = "SELECT 
            r.id AS penalizacao_id,
            r.descricao AS motivo,
            r.tipoBanimento,
            r.dataBanimento,
            u.login AS login_usuario,
            u.email AS email_usuario,
            d.id AS denuncia_id,
            d.dataDenuncia
        FROM Registro r
        JOIN Denuncia d ON r.id_denuncia = d.id
        JOIN Cliente c ON d.id_cliente = c.id
        JOIN Usuario u ON c.id_usuario = u.id
        ORDER BY r.dataBanimento DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Penalizações</title>
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

        body .back-button-container .btn-voltar {
            position: fixed;
            top: 90px;
            left: 20px;
            background-color: #6c757d;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            z-index: 999;
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

    <div class="back-button-container">
        <button onclick="window.history.back()" class="btn-voltar">← Voltar</button>
    </div>

    <div class="content">
        <h1>Histórico Penalizações</h1>

        <div class="denuncias-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuário</th>
                        <th>Email</th>
                        <th>Data da Denúncia</th>
                        <th>Tipo de Penalização</th>
                        <th>Data da Penalização</th>
                        <th>Motivo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['penalizacao_id']) ?></td>
                            <td><?= htmlspecialchars($row['login_usuario']) ?></td>
                            <td><?= htmlspecialchars($row['email_usuario']) ?></td>
                            <td><?= htmlspecialchars($row['dataDenuncia']) ?></td>
                            <td><?= htmlspecialchars($row['tipoBanimento']) ?></td>
                            <td><?= htmlspecialchars($row['dataBanimento']) ?></td>
                            <td><?= htmlspecialchars($row['motivo']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
