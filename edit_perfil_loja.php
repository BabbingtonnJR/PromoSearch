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

    $update_sql = "UPDATE Usuario SET nome = ?, endereco = ?, numero = ?, telefone = ?, email = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssi", $nome_novo, $endereco_novo, $numero_novo, $telefone_novo, $email_novo, $id);

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
                    <li><a href="#">Página 3</a></li>
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

<div class="profile-container">
    <div class="profile-header">
        <h1>Editar Perfil</h1>
    </div>
    <form method="POST">
        <div class="profile-form">
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
