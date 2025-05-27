<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

if (!isset($_GET['id_loja'])) {
    echo "ID da loja não fornecido.";
    exit();
}

include "connection.php";

$id_usuario = $_SESSION['id_usuario'];
$id_loja = intval($_GET['id_loja']);

$sqlLoja = "SELECT U.nome AS nomeLoja, U.endereco, U.numero
            FROM Loja L
            JOIN Usuario U ON L.id_usuario = U.id
            WHERE L.id = $id_loja";
$resultLoja = $conn->query($sqlLoja);

if (!$resultLoja || $resultLoja->num_rows === 0) {
    echo "Loja não encontrada.";
    exit();
}

$loja = $resultLoja->fetch_assoc();

$sqlPromocoes = "SELECT P.id AS id_promocao, P.nomeProduto, P.precoInicial, P.precoPromocional, P.quantidade
                 FROM Historico H
                 JOIN ListaPromocao LP ON H.id_listaPromocao = LP.id
                 JOIN Promocao P ON LP.id_promocao = P.id
                 WHERE H.id_loja = $id_loja AND P.quantidade > 0
                 GROUP BY P.id";

$resultPromocoes = $conn->query($sqlPromocoes);

$promocoes = [];
if ($resultPromocoes && $resultPromocoes->num_rows > 0) {
    while ($row = $resultPromocoes->fetch_assoc()) {
        $promocoes[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promoções da Loja</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        ul li a{
            font-family: arial;
            text-decoration: none;
            color: white;
        }

        li {
            list-style-type: none;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        h2, h3 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #333;
            color: white;
        }

        a.back {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007BFF;
        }

        a.back:hover {
            text-decoration: underline;
        }

        button {
            padding: 6px 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
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
                    <li><a href="index.php">Mapa</a></li>
                    <li><a href="promocoes_salvas.php">Salvos</a></li>
                    <li><a href="logout.php">Sair</a></li>
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

<div class="container">
    <h2>Promoções da Loja</h2>
    <h3><?= htmlspecialchars($loja['nomeLoja']) ?></h3>
    <p style="text-align:center;"><?= htmlspecialchars($loja['endereco']) ?>, <?= htmlspecialchars($loja['numero']) ?></p>

    <?php if (count($promocoes) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço Inicial</th>
                    <th>Preço Promocional</th>
                    <th>Quantidade</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($promocoes as $promo): ?>
                    <tr>
                        <td><?= htmlspecialchars($promo['nomeProduto']) ?></td>
                        <td>R$ <?= number_format($promo['precoInicial'], 2, ',', '.') ?></td>
                        <td>R$ <?= number_format($promo['precoPromocional'], 2, ',', '.') ?></td>
                        <td><?= intval($promo['quantidade']) ?></td>
                        <td>
                            <form method="POST" action="salvar_promocao.php">
                                <input type="hidden" name="id_promocao" value="<?= $promo['id_promocao'] ?>">
                                <button type="submit">Salvar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center; margin-top:20px;">Nenhuma promoção ativa encontrada para esta loja.</p>
    <?php endif; ?>

    <div style="text-align:center;">
        <a class="back" href="index.php">← Voltar para o mapa</a>
    </div>
</div>

</body>
</html>
