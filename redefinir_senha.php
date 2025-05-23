<?php
include "connection.php";

$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmaSenha = $_POST['confirma_senha'] ?? '';

    if ($novaSenha !== $confirmaSenha) {
        $erro = "As senhas não coincidem.";
    } elseif (strlen($novaSenha) < 6) {
        $erro = "A senha deve ter no mínimo 6 caracteres.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM Usuario WHERE reset_token = ? AND reset_expira > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $userId = $user['id'];

            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

            $update = $conn->prepare("UPDATE Usuario SET senha = ?, reset_token = NULL, reset_expira = NULL WHERE id = ?");
            $update->bind_param("si", $senhaHash, $userId);
            $update->execute();

            $sucesso = "Senha redefinida com sucesso. Você já pode fazer login.";
        } else {
            $erro = "Token inválido ou expirado.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <h2>Redefinir Senha</h2>

        <?php if (isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
        <?php if (isset($sucesso)) echo "<p style='color:green;'>$sucesso</p>"; ?>

        <?php if (!isset($sucesso)): ?>
        <form method="POST">
            <label for="nova_senha">Nova Senha</label>
            <input type="password" id="nova_senha" name="nova_senha" required>

            <label for="confirma_senha">Confirmar Senha</label>
            <input type="password" id="confirma_senha" name="confirma_senha" required>

            <br><br>
            <button type="submit">Redefinir Senha</button>
        </form>
        <?php endif; ?>

        <br>
        <a href="login.html">Voltar ao login</a>
    </div>
</body>
</html>
