<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

include "connection.php";

$id = $_SESSION['id_usuario'];

$sql = "SELECT nome, endereco, telefone, email FROM Usuario WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($nome, $endereco, $telefone, $email);
    $stmt->fetch();
} else {
    echo "Usuário não encontrado.";
    exit();
}

if (isset($_POST['delete'])) {
    $delete_sql = "DELETE FROM Loja WHERE id_usuario = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $id);
    $delete_user_sql = "DELETE FROM Usuario WHERE id = ?";
    $delete_user_stmt = $conn->prepare($delete_user_sql);
    $delete_user_stmt->bind_param("i", $id);

    if ($delete_stmt->execute()) {
        if ($delete_user_stmt->execute()) {
            session_destroy();
            header("Location: login.html");
            exit();
        }
    } else {
        echo "Erro ao tentar deletar a conta.";
    }

    $delete_stmt->close();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuário</title>
    <link rel="stylesheet" href="perfil.css">

    <style>
        ul li a{
            font-family: arial;
            text-decoration: none;
            color: white;
        }

        li {
            list-style-type: none;
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
                    <li><a href="produtos.php">Produtos</a></li>
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

<div class="profile-container">
    <div class="profile-header">
        <h1>Meu Perfil</h1>
    </div>
    <div class="profile-content">
        <div class="profile-photo">
            <img src="exibir_foto.php" alt="Foto de Perfil" style="width: 150px; height: 150px; border-radius: 50%;">
        </div>
        <div class="profile-details">
            <h2><?php echo $nome; ?></h2>
            <p><strong>Endereço:</strong> <?php echo $endereco;  ?></p>
            <p><strong>Telefone:</strong> <?php echo $telefone; ?></p>
            <p><strong>Email:</strong> <?php echo $email; ?></p>
        </div>
    </div>

    <div class="profile-actions">
        <form method="POST" onsubmit="return confirmDeletion();">
            <button type="submit" name="delete" class="action-btn delete-btn">Deletar Conta</button>
        </form>
        <a href="edit_perfil_loja.php"><button class="action-btn edit-btn">Editar Perfil</button></a>
    </div>
</div>

<script>
    function confirmDeletion() {
        return confirm("Tem certeza de que deseja excluir sua conta? Esta ação não pode ser desfeita.");
    }
</script>

</body>
</html>