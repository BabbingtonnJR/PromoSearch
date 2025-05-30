<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

include "connection.php";

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
    $id_promocao = $_GET['id'];

    $query = "SELECT * FROM Promocao WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_promocao);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $nomeProduto = $row['nomeProduto'];
        $precoInicial = $row['precoInicial'];
        $precoPromocional = $row['precoPromocional'];
        $quantidade = $row['quantidade'];
        $tipo = $row['tipo'];
        $imagem = $row['imagem'];
    } else {
        header("Location: produtos.php?error=notfound");
        exit();
    }

    mysqli_stmt_close($stmt);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_promocao = $_POST['id_promocao'];
    $nomeProduto = $_POST['nomeProduto'];
    $precoInicial = $_POST['precoInicial'];
    $precoPromocional = $_POST['precoPromocional'];
    $quantidade = $_POST['quantidade'];
    $tipo = $_POST['tipo'];

    $imagem_blob = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $imagem_blob = file_get_contents($_FILES['imagem']['tmp_name']);
    } else {
        $query_imagem = "SELECT imagem FROM Promocao WHERE id = ?";
        $stmt_imagem = mysqli_prepare($conn, $query_imagem);
        mysqli_stmt_bind_param($stmt_imagem, "i", $id_promocao);
        mysqli_stmt_execute($stmt_imagem);
        $result_imagem = mysqli_stmt_get_result($stmt_imagem);
        $row_imagem = mysqli_fetch_assoc($result_imagem);
        $imagem_blob = $row_imagem['imagem'];
        mysqli_stmt_close($stmt_imagem);
    }

    $query = "UPDATE Promocao 
              SET nomeProduto = ?, precoInicial = ?, precoPromocional = ?, quantidade = ?, tipo = ?, imagem = ? 
              WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sddisbi", 
        $nomeProduto, 
        $precoInicial, 
        $precoPromocional, 
        $quantidade, 
        $tipo, 
        $imagem_blob, 
        $id_promocao
    );

    mysqli_stmt_send_long_data($stmt, 5, $imagem_blob);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: produtos.php?updated=1");
        exit();
    } else {
        header("Location: editar_promocao.php?id=$id_promocao&error=updatefail");
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Promoção</title>
    <link rel="stylesheet" href="produtos.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        ul li a{
            font-family: arial;
            text-decoration: none;
            color: white;
        }

        li {
            list-style-type: none;
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
                        <img src="exibir_foto.php" alt="Foto de Perfil" style="width: 40px; height: 40px; border-radius: 50%;">
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="back-button-container">
        <button onclick="window.history.back()" class="btn-voltar">← Voltar</button>
    </div>

    <div class="content">
        <div class="product-form">
            <h2>Editar Promoção</h2>
            <form method="POST" action="editar_promocao.php" enctype="multipart/form-data">
                <input type="hidden" name="id_promocao" value="<?php echo htmlspecialchars($id_promocao); ?>">

                <div class="form-group">
                    <label for="imagem">Imagem do Produto</label>
                    <input type="file" id="imagem" name="imagem" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="nomeProduto">Nome do Produto</label>
                    <input type="text" id="nomeProduto" name="nomeProduto" value="<?php echo htmlspecialchars($nomeProduto); ?>" required>
                </div>

                <div class="form-group">
                    <label for="precoInicial">Preço Inicial</label>
                    <input type="number" id="precoInicial" name="precoInicial" step="0.01" min="0" value="<?php echo htmlspecialchars($precoInicial); ?>" required>
                </div>

                <div class="form-group">
                    <label for="precoPromocional">Preço Promocional</label>
                    <input type="number" id="precoPromocional" name="precoPromocional" step="0.01" min="0" value="<?php echo htmlspecialchars($precoPromocional); ?>" required>
                </div>

                <div class="form-group">
                    <label for="quantidade">Quantidade Disponível</label>
                    <input type="number" id="quantidade" name="quantidade" min="1" value="<?php echo htmlspecialchars($quantidade); ?>" required>
                </div>

                <div class="form-group">
                    <label for="tipo">Tipo de Produto</label>
                    <select id="tipo" name="tipo" required>
                        <option value="">Selecione um tipo</option>
                        <option value="eletronicos" <?php if ($tipo == "eletronicos") echo "selected"; ?>>Eletrônicos</option>
                        <option value="roupas" <?php if ($tipo == "roupas") echo "selected"; ?>>Roupas</option>
                        <option value="alimentos" <?php if ($tipo == "alimentos") echo "selected"; ?>>Alimentos</option>
                        <option value="moveis" <?php if ($tipo == "moveis") echo "selected"; ?>>Móveis</option>
                        <option value="outros" <?php if ($tipo == "outros") echo "selected"; ?>>Outros</option>
                    </select>
                </div>

                <?php if (!empty($imagem)) : ?>
                    <div class="form-group">
                        <p>Imagem Atual:</p>
                        <img src="exibir_imagem.php?id=<?php echo $id_promocao; ?>" alt="Imagem do Produto" style="max-width: 200px;">
                    </div>
                <?php endif; ?>

                <div class="form-actions">
                    <button type="submit" class="btn btn-add">SALVAR ALTERAÇÕES</button>
                    <a href="produtos.php" class="btn btn-cancel">CANCELAR</a>
                </div>
            </form>

        </div>
    </div>
</body>
</html>
