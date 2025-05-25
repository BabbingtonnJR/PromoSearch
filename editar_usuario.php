<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'Administrador') {
    header("Location: login.html");
    exit();
}

include "connection.php";

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "Nenhum usuário selecionado para edição.";
    header("Location: gerenciar_usuarios.php");
    exit();
}

$user_id = (int)$_GET['id'];

$sql = "SELECT U.id, U.nome, U.login, U.email, 
        CASE 
            WHEN EXISTS (SELECT 1 FROM Cliente C WHERE C.id_usuario = U.id) THEN 'Cliente'
            WHEN EXISTS (SELECT 1 FROM Loja L WHERE L.id_usuario = U.id) THEN 'Loja'
            WHEN EXISTS (SELECT 1 FROM Administrador A WHERE A.id_usuario = U.id) THEN 'Administrador'
            ELSE 'Desconhecido'
        END AS tipo
        FROM Usuario U
        WHERE U.id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erro na preparação da consulta: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['message'] = "Usuário não encontrado.";
    header("Location: gerenciar_usuarios.php");
    exit();
}

$user = $result->fetch_assoc();

$query = "SELECT email FROM Usuario WHERE id = $user_id";
$result_email = mysqli_query($conn, $query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $tipo = $_POST['tipo'];
    
    if (empty($nome) || empty($email)) {
        $error = "Nome e email são obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email inválido.";
    } elseif (mysqli_num_rows($result) > 0) {
        $error = "Email já está em uso.";
    } else {
        $update_sql = "UPDATE Usuario SET nome = ?, email = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $nome, $email, $user_id);
        $update_stmt->execute();
        
        $_SESSION['message'] = "Usuário atualizado com sucesso!";
        header("Location: gerenciar_usuarios.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="styles_adm.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <h2>Sistema Admin</h2>
            </div>
            <ul class="nav-links">
                <li><a href="index_adm.php">Início</a></li>
                <li class="dropdown">
                    <button class="dropdown-btn">Usuários</button>
                    <ul class="dropdown-content">
                        <li><a href="gerenciar_usuarios.php">Gerenciar Usuários</a></li>
                        <li><a href="adicionar_usuario.php">Adicionar Usuário</a></li>
                    </ul>
                </li>
                <li class="profile">
                    <a href="perfil_admin.php">
                        <img src="https://w7.pngwing.com/pngs/1000/665/png-transparent-computer-icons-profile-s-free-angle-sphere-profile-cliparts-free.png" alt="Perfil Admin">
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="content">
        <h1>Editar Usuário</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="edit-container">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($user['nome']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="login">Login:</label>
                    <input type="text" id="login" name="login" value="<?= htmlspecialchars($user['login']) ?>" disabled>
                    <small style="color: #666;">(Login não pode ser alterado)</small>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="tipo">Tipo de Usuário:</label>
                    <select id="tipo" name="tipo" <?= $user['tipo'] === 'Administrador' && $_SESSION['id_usuario'] === $user['id'] ? 'disabled' : '' ?>>
                        <option value="Cliente" <?= $user['tipo'] === 'Cliente' ? 'selected' : '' ?>>Cliente</option>
                        <option value="Loja" <?= $user['tipo'] === 'Loja' ? 'selected' : '' ?>>Loja</option>
                        <option value="Administrador" <?= $user['tipo'] === 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                    <?php if ($user['tipo'] === 'Administrador' && $_SESSION['id_usuario'] === $user['id']): ?>
                        <small style="color: #666;">(Você não pode alterar seu próprio tipo)</small>
                    <?php endif; ?>
                </div>
                
                <div class="form-actions">
                    <a href="gerenciar_usuarios.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
if (isset($update_stmt)) {
    $update_stmt->close();
}
$conn->close();
?>