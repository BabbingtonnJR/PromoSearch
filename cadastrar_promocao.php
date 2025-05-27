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
    <title>Cadastrar Promoção</title>

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
                        <li><a href="produtos.php">Promoções</a></li>
                        <li><a href="index_loja.php">Mapa</a></li>
                        <li><a href="logout.php">Sair</a></li>
                    </ul>
                </li>
                <li class="profile">
                    <a href="perfil_loja.php">
                        <img src="https://w7.pngwing.com/pngs/1000/665/png-transparent-computer-icons-profile-s-free-angle-sphere-profile-cliparts-free.png" alt="Perfil">
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="content">
        <div class="product-form">
            <h2>Cadastrar Nova Promoção</h2>
            <form action="processar_promocao.php" method="POST">
                <div class="form-group">
                    <label for="nomeProduto">Nome do Produto</label>
                    <input type="text" id="nomeProduto" name="nomeProduto" required>
                </div>
                <div class="form-group">
                    <label for="precoInicial">Preço Inicial</label>
                    <input type="number" id="precoInicial" name="precoInicial" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="precoPromocional">Preço Promocional</label>
                    <input type="number" id="precoPromocional" name="precoPromocional" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="quantidade">Quantidade Disponível</label>
                    <input type="number" id="quantidade" name="quantidade" min="1" required>
                </div>
                <div class="form-group">
                    <label for="tipo">Tipo de Produto</label>
                    <select id="tipo" name="tipo" required>
                        <option value="">Selecione um tipo</option>
                        <option value="eletronicos">Eletrônicos</option>
                        <option value="roupas">Roupas</option>
                        <option value="alimentos">Alimentos</option>
                        <option value="moveis">Móveis</option>
                        <option value="outros">Outros</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-add">ADICIONAR PROMOÇÃO</button>
                    <a href="produtos.php" class="btn btn-cancel">CANCELAR</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>