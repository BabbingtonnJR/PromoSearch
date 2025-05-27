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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $login = trim($_POST['login']);
    $email = trim($_POST['email']);
    
    if (empty($nome) || empty($login) || empty($email)) {
        $error = "Nome, login e email são obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email inválido.";
    } else {
        $check_sql = "SELECT id FROM Usuario WHERE (email = ? OR login = ?) AND id != ? ";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt -> bind_param("ssi", $email, $login, $user_id);
        $check_stmt -> execute();
        $check_result = $check_stmt -> get_result();

        
        if ($check_result->num_rows > 0) {
            $error = "Login ou email já está em uso por outro usuário.";
        } else {
            $update_sql = "UPDATE Usuario SET nome = ?, login = ?, email = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sssi", $nome, $login, $email, $user_id);
            $update_stmt->execute();
    
            $_SESSION['message'] = "Usuário atualizado com sucesso!";
            header("Location: gerenciar_usuarios.php");
            exit();
        }
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
                    <input type="text" id="login" name="login" value="<?= htmlspecialchars($user['login']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
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