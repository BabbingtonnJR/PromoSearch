<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'Administrador') {
    header("Location: login.html");
    exit();
}

include "connection.php";

$sql = "SELECT 
            d.id AS denuncia_id,
            d.descricao AS denuncia_desc,
            d.estado,
            d.dataDenuncia,
            u.login AS cliente_login,
            u.email AS cliente_email,
            p.nomeProduto,
            p.precoPromocional,
            r.descricao AS admin_acao,
            r.dataBanimento,
            r.tipoBanimento
        FROM Denuncia d
        JOIN Cliente c ON d.id_cliente = c.id
        JOIN Usuario u ON c.id_usuario = u.id
        JOIN Promocao p ON d.id_promocao = p.id
        LEFT JOIN Registro r ON d.id = r.id_denuncia
        ORDER BY d.estado ASC, d.dataDenuncia DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Denúncias</title>
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
        <h1>Visualizar Denúncias</h1>
        
        
        <div class="denuncias-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Descrição</th>
                        <th>Estado</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['denuncia_id']) ?></td>
                            <td><?= htmlspecialchars($row['dataDenuncia']) ?></td>
                            <td>
                                <?= htmlspecialchars($row['cliente_login']) ?><br>
                                <small><?= htmlspecialchars($row['cliente_email']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($row['nomeProduto']) ?></td>
                            <td>R$ <?= number_format($row['precoPromocional'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($row['denuncia_desc']) ?></td>
                            <td class="<?= $row['estado'] ? 'estado-resolvido' : 'estado-pendente' ?>">
                                <?= $row['estado'] ? 'Resolvido' : 'Pendente' ?>
                                <?php if ($row['estado'] && $row['admin_acao']): ?>
                                    <br><small><?= htmlspecialchars($row['tipoBanimento']) ?> (<?= htmlspecialchars($row['dataBanimento']) ?>)</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if (!$row['estado']): ?>
                                        <a href="resolver_denuncia.php?id=<?= $row['denuncia_id'] ?>" class="btn btn-success">Resolver</a>
                                    <?php else: ?>
                                        <span class="btn btn-primary" style="background-color: #95a5a6; cursor: default;">Resolvido</span>
                                    <?php endif; ?>
                                </div>
                            </td>
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