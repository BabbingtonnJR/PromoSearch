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

if (isset($_POST['update'])) {
    $nome_novo = $_POST['nome'];
    $endereco_novo = $_POST['endereco'];
    $telefone_novo = $_POST['telefone'];
    $email_novo = $_POST['email'];

    $update_sql = "UPDATE Usuario SET nome = ?, endereco = ?, telefone = ?, email = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssi", $nome_novo, $endereco_novo, $telefone_novo, $email_novo, $id);

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
    </div>
</nav>
</body>
</html>