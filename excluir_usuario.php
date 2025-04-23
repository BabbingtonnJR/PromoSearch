<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'Administrador') {
    header("Location: login.html");
    exit();
}

include "connection.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "ID de usuário inválido.";
    header("Location: gerenciar_usuarios.php");
    exit();
}

$user_id = (int)$_GET['id'];

if ($_SESSION['id_usuario'] === $user_id) {
    $_SESSION['message'] = "Você não pode excluir seu próprio usuário.";
    header("Location: gerenciar_usuarios.php");
    exit();
}

$tipo = '';
if ($conn->query("SELECT 1 FROM Cliente WHERE id_usuario = $user_id LIMIT 1")->num_rows > 0) {
    $tipo = 'Cliente';
} elseif ($conn->query("SELECT 1 FROM Loja WHERE id_usuario = $user_id LIMIT 1")->num_rows > 0) {
    $tipo = 'Loja';
} elseif ($conn->query("SELECT 1 FROM Administrador WHERE id_usuario = $user_id LIMIT 1")->num_rows > 0) {
    $tipo = 'Administrador';
} else {
    $_SESSION['message'] = "Tipo de usuário não identificado.";
    header("Location: gerenciar_usuarios.php");
    exit();
}

$conn->begin_transaction();

try {
    switch ($tipo) {
        case 'Cliente':
            $delete_specific = "DELETE FROM Cliente WHERE id_usuario = ?";
            break;
        case 'Loja':
            $delete_specific = "DELETE FROM Loja WHERE id_usuario = ?";
            break;
        case 'Administrador':
            $delete_specific = "DELETE FROM Administrador WHERE id_usuario = ?";
            break;
    }

    $stmt_specific = $conn->prepare($delete_specific);
    $stmt_specific->bind_param("i", $user_id);
    $stmt_specific->execute();
    $stmt_specific->close();

    $delete_user = "DELETE FROM Usuario WHERE id = ?";
    $stmt_user = $conn->prepare($delete_user);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $stmt_user->close();

    $conn->commit();

    $_SESSION['message'] = "Usuário ($tipo) excluído com sucesso!";
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['message'] = "Erro ao excluir usuário: " . $e->getMessage();
}

header("Location: gerenciar_usuarios.php");
exit();
?>