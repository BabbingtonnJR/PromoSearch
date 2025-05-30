<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

include 'connection.php';

$id_usuario = $_SESSION['id_usuario'];

$query_loja = "SELECT id FROM Loja WHERE id_usuario = ?";
$stmt_loja = mysqli_prepare($conn, $query_loja);
mysqli_stmt_bind_param($stmt_loja, "i", $id_usuario);
mysqli_stmt_execute($stmt_loja);
$result_loja = mysqli_stmt_get_result($stmt_loja);

if ($row_loja = mysqli_fetch_assoc($result_loja)) {
    $id_loja = $row_loja['id'];
} else {
    echo "<p>Loja não encontrada.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="produtos.css">
    <title>Promoções</title>
    <style>
        .add-promotion-btn {
            background-color: #4CAF50;
            text-decoration: none;
            font-family: arial;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .add-promotion-btn:hover {
            background-color: #45a049;
        }
        .product-card.selected {
            border: 3px solid #4CAF50;
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
        }
        .selection-controls {
            margin: 20px 0;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 8px;
            display: none;
        }

        .edit-button, .delete-button {
            display: inline-block;
            padding: 6px 12px;
            margin: 5px 4px 0 0;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .edit-button {
            background-color: #007bff;
        }

        .edit-button:hover {
            background-color: #0056b3;
        }

        .delete-button {
            background-color: #dc3545;
        }

        .delete-button:hover {
            background-color: #b02a37;
        }

        .product-actions {
            margin-top: 10px;
        }

        .hidden {
            display: none;
        }

        ul li a{
            font-family: arial;
            text-decoration: none;
            color: white;
        }

        li {
            list-style-type: none;
        }

        body .back-button-container .btn-voltar {
            position: relative;
            top: 0;
            left: 0;
            background-color: #6c757d;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
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
                        <li><a href="index_loja.php">Mapa</a></li>
                        <li><a href="produtos.php">Promoções</a></li>
                        <li><a href="logout.php">Sair</a></li>
                    </ul>
                </li>
                <li class="profile">
                    <a href="perfil_loja.php">
                        <img src="exibir_foto.php" alt="Foto de Perfil" style="width: 40px; height: 40px; border-radius: 50%;">
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="content">
        <div class="back-button-container">
            <button onclick="window.history.back()" class="btn-voltar">← Voltar</button>
        </div>
        <div class="products-header">
            <h2>Promoções Cadastradas</h2>
            <br>
            <a href="cadastrar_promocao.php" class="add-promotion-btn">Adicionar Promoção</a>
        </div>
        
        <br>

        <div class="product-grid">
            <?php
            $query = "SELECT * FROM Promocao WHERE id_loja = $id_loja";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="product-card" data-id="' . $row['id'] . '">';
                    echo '<div class="product-image">';
                    echo '<img src="exibir_imagem.php?id=' . $row['id'] . '" alt="' . htmlspecialchars($row['nomeProduto']) . '" style="width: 250px; height: 250px; object-fit: cover;">';
                    echo '</div>';
                    echo '<div class="product-info">';
                    echo '<div class="product-name">' . $row['nomeProduto'] . '</div>';
                    echo '<div class="product-price">De: R$ ' . number_format($row['precoInicial'], 2, ',', '.') . '</div>';
                    echo '<div class="product-promo-price">Por: R$ ' . number_format($row['precoPromocional'], 2, ',', '.') . '</div>';
                    echo '<div class="product-quantity">Quantidade: ' . $row['quantidade'] . '</div>';
                    echo '<div class="product-category">Categoria: ' . ucfirst($row['tipo']) . '</div>';

                    echo '<div class="product-actions">';
                    echo '<a href="editar_promocao.php?id=' . $row['id'] . '" class="edit-button">Editar</a>';
                    echo '<form action="excluir_promocao.php" method="POST" style="display:inline;">';
                    echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
                    echo '<button type="submit" class="delete-button" onclick="return confirm(\'Tem certeza que deseja excluir esta promoção?\')">Excluir</button>';
                    echo '</form>';
                    echo '</div>';

                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>Nenhuma promoção cadastrada ainda.</p>';
            }
            ?>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const backBtn = document.querySelector('.btn-voltar');
        backBtn.addEventListener('click', () => window.history.back());
    });
</script>

</body>
</html>