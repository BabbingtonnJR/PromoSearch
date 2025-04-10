<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.html");
    exit();
}

include "connection.php";

$id = $_SESSION['id'];

$sql = "SELECT nome, sobrenome, endereco, numero_residencia, telefone, email FROM Cliente WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($nome, $sobrenome, $endereco, $numresicencia, $telefone, $email);
    $stmt->fetch();
} else {
    echo "Usuário não encontrado.";
    exit();
}

if (isset($_POST['update'])) {
    $nome_novo = $_POST['nome'];
    $sobrenome_novo = $_POST['sobrenome'];
    $endereco_novo = $_POST['endereco'];
    $numresicencia_novo = $_POST['numero_residencia'];
    $telefone_novo = $_POST['telefone'];
    $email_novo = $_POST['email'];

    $update_sql = "UPDATE Cliente SET nome = ?, sobrenome = ?, endereco = ?, numero_residencia = ?, telefone = ?, email = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssssi", $nome_novo, $sobrenome_novo, $endereco_novo, $numresicencia_novo, $telefone_novo, $email_novo, $id);

    if ($update_stmt->execute()) {
?>
<script>
alert("Perfil atualizado com sucesso!");
</script>
<?php
        header("Location: perfil.php");
        exit();
    } else {
?>
<script>
alert("Erro ao atualizar o perfil");
</script>
<?php
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
                    <li><a href="#">Página 1</a></li>
                    <li><a href="#">Página 2/a></li>
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
        <h1>Editar Perfil</h1>
    </div>
    <form method="POST">
        <div class="profile-form">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo $nome; ?>" required>

            <label for="sobrenome">Sobrenome:</label>
            <input type="text" id="sobrenome" name="sobrenome" value="<?php echo $sobrenome; ?>" required>

            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco" value="<?php echo $endereco; ?>" required>

            <label for="numero_residencia">Número da Residência:</label>
            <input type="text" id="numero_residencia" name="numero_residencia" value="<?php echo $numresicencia; ?>" required>

            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" value="<?php echo $telefone; ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>

            <button type="submit" name="update">Atualizar Perfil</button>
        </div>
    </form>
</div>

</body>
</html>
