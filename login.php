<?php
include "connection.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST['txtUser']);
    $senha = trim($_POST['txtPsw']);
    $tipo_usuario = $_POST['tipo_usuario'] ?? '';

    switch ($tipo_usuario) {
        case "cliente":
            $tabela = "Cliente";
            break;
        case "loja":
            $tabela = "Loja";
            break;
        case "admin":
            $tabela = "Administrador";
            break;
        default:
?>
<script>
alert('Selecione um tipo de usuário válido!');
window.history.go(-1);
</script>
<?php
            exit();
    }

    $stmt = $conn->prepare("SELECT id, nome, senha FROM Cliente WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $nome, $senha_hash);
        $stmt->fetch();

        if (password_verify($senha, $senha_hash)) {
            $_SESSION['id'] = $id;
            $_SESSION['nome'] = $nome;
            $_SESSION['tipo'] = $tipo_usuario;

            if ($tipo_usuario == "cliente") {
                header("Location: index.php");
            } elseif ($tipo_usuario == "loja") {
                header("Location: index_loja.php");
            } elseif ($tipo_usuario == "admin") {
                header("Location: index_adm.php");
            }
            exit();
        } else {
?>
<script>
alert('Usuário ou senha incorretos!');
window.history.go(-1);
</script>
<?php
        }
    } else {
?>
<script>
alert('Usuário não encontrado!');
window.history.go(-1);
</script>
<?php
    }
    
    $stmt->close();
    $conn->close();
}
?>
