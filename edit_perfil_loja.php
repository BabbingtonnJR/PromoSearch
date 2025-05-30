<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

include "connection.php";

$id = $_SESSION['id_usuario'];

$sql = "SELECT nome, endereco, numero, telefone, email FROM Usuario WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($nome, $endereco, $numero, $telefone, $email);
    $stmt->fetch();
} else {
    echo "Usuário não encontrado.";
    exit();
}

if (isset($_POST['update'])) {
    $nome_novo = $_POST['nome'];
    $endereco_novo = $_POST['endereco'];
    $numero_novo = $_POST['numero'];
    $telefone_novo = $_POST['telefone'];
    $email_novo = $_POST['email'];

    $foto_binaria = null;
    $tem_foto = false;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_binaria = file_get_contents($foto_tmp);
        $tem_foto = true;
    }

    if ($tem_foto) {
        $update_sql = "UPDATE Usuario SET nome = ?, endereco = ?, numero = ?, telefone = ?, email = ?, foto = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssssssi", $nome_novo, $endereco_novo, $numero_novo, $telefone_novo, $email_novo, $foto_binaria, $id);
    } else {
        $update_sql = "UPDATE Usuario SET nome = ?, endereco = ?, numero = ?, telefone = ?, email = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssssi", $nome_novo, $endereco_novo, $numero_novo, $telefone_novo, $email_novo, $id);
    }

    if ($update_stmt->execute()) {
        echo "<script>alert('Perfil atualizado com sucesso!'); window.location.href='perfil_loja.php';</script>";
        exit();
    } else {
        echo "<script>alert('Erro ao atualizar o perfil');</script>";
    }

    $update_stmt->close();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="edit_perfil.css">
    <title>Editar Perfil</title>
    <style>
        ul li a {
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

<div class="back-button-container">
    <button onclick="window.history.back()" class="btn-voltar">← Voltar</button>
</div>

<div class="profile-container">
    <div class="profile-header">
        <h1>Editar Perfil</h1>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <div class="profile-form">
            <label for="foto">Foto de Perfil:</label>
            <input type="file" id="foto" name="foto" accept="image/*">

            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>" required>

            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco" value="<?php echo htmlspecialchars($endereco); ?>" required>

            <label for="numero">Número da Residência:</label>
            <input type="text" id="numero" name="numero" value="<?php echo htmlspecialchars($numero); ?>" required>

            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($telefone); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <button type="submit" name="update">Atualizar Perfil</button>
        </div>
    </form>
</div>

</body>
</html>
