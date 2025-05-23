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
                    <li><a href="#">Página 2</a></li>
                    <li><a href="#">Página 3</a></li>
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

<div class="profile-container">
    <div class="profile-header">
        <h1>Meu Perfil</h1>
    </div>
    <div class="profile-content">
        <div class="profile-photo">
            <img src="https://w7.pngwing.com/pngs/1000/665/png-transparent-computer-icons-profile-s-free-angle-sphere-profile-cliparts-free.png" alt="Foto do Perfil">
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
        <a href="edit_perfil.php"><button class="action-btn edit-btn">Editar Perfil</button></a>
    </div>
</div>

<script>
    function confirmDeletion() {
        return confirm("Tem certeza de que deseja excluir sua conta? Esta ação não pode ser desfeita.");
    }
</script>

</body>
</html>