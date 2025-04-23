<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

include 'connection.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="produtos.css">
    <title>Promoções</title>
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
                        <li><a href="produtos.php">Promoções</a></li>
                        <li><a href="index_loja.php">Mapa</a></li>
                        <li><a href="#">Página 3</a></li>
                    </ul>
                </li>
                <li class="profile">
                    <a href="#">
                        <img src="https://w7.pngwing.com/pngs/1000/665/png-transparent-computer-icons-profile-s-free-angle-sphere-profile-cliparts-free.png" alt="Perfil">
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="content">
        <div class="products-header">
            <h2>Promoções Cadastradas</h2>
            <br>
            <a href="cadastrar_promocao.php" class="add-promotion-btn">Adicionar Promoção</a>
        </div>
        <br>
        
        <div class="product-grid">
            <?php
            $query = "SELECT * FROM Promocao";
            $result = mysqli_query($conn, $query);
            
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="product-card">';
                    echo '<div class="product-image">';
                    echo '<img src="https://via.placeholder.com/250" alt="'.$row['nomeProduto'].'">';
                    echo '</div>';
                    echo '<div class="product-info">';
                    echo '<div class="product-name">'.$row['nomeProduto'].'</div>';
                    echo '<div class="product-price">De: R$ '.number_format($row['precoInicial'], 2, ',', '.').'</div>';
                    echo '<div class="product-promo-price">Por: R$ '.number_format($row['precoPromocional'], 2, ',', '.').'</div>';
                    echo '<div class="product-quantity">Quantidade: '.$row['quantidade'].'</div>';
                    echo '<div class="product-category">Categoria: '.ucfirst($row['tipo']).'</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>Nenhuma promoção cadastrada ainda.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>