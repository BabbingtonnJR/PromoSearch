<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

include "connection.php";

$id_usuario = $_SESSION['id_usuario'];

$stmt = $conn->prepare("
    SELECT P.id AS id_promocao, P.nomeProduto, P.precoInicial, P.precoPromocional,
           U.nome AS nomeLoja, L.id AS id_loja
    FROM PromocoesSalvas PS
    JOIN Cliente C ON PS.id_cliente = C.id
    JOIN Promocao P ON PS.id_promocao = P.id
    JOIN Loja L ON P.id_loja = L.id
    JOIN Usuario U ON L.id_usuario = U.id
    WHERE C.id_usuario = ?
");


$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

$promocoes = [];
while ($row = $result->fetch_assoc()) {
    $promocoes[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Promoções Salvas</title>
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
                    <img src="exibir_foto.php" alt="Foto de Perfil" style="width: 40px; height: 40px; border-radius: 50%;">
                </a>
            </li>
        </ul>
    </div>
</nav>

<div class="content">
    <h1>Promoções Salvas</h1>
    <?php if (empty($promocoes)): ?>
        <p>Você ainda não salvou nenhuma promoção.</p>
    <?php else: ?>
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Loja</th>
                    <th>Produto</th>
                    <th>De</th>
                    <th>Por</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($promocoes as $promo): ?>
                <tr>
                    <td>
                        <img src="exibir_imagem.php?id=<?= $promo['id_promocao'] ?>" 
                            alt="<?= htmlspecialchars($promo['nomeProduto']) ?>" 
                            style="width: 80px; height: 80px; object-fit: cover;">
                    </td>

                    <td><?= htmlspecialchars($promo['nomeLoja']) ?></td>
                    <td><?= htmlspecialchars($promo['nomeProduto']) ?></td>
                    <td>R$ <?= number_format($promo['precoInicial'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format($promo['precoPromocional'], 2, ',', '.') ?></td>
                    <td>
                        <form method="POST" action="remover_promocao.php" style="display:inline;">
                            <input type="hidden" name="id_promocao" value="<?= $promo['id_promocao'] ?>">
                            <button type="submit">Remover</button>
                        </form>

                        <form method="GET" action="produtos_loja.php" style="display:inline; margin-left: 5px;">
                            <input type="hidden" name="id_loja" value="<?= $promo['id_loja'] ?>">
                            <button type="submit">Ver Produtos da Loja</button>
                        </form>
                    </td>

                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
