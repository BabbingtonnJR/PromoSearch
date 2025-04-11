<?php
include "connection.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST['txtUser']);
    $senha = trim($_POST['txtPsw']);
    $tipo_selecionado = $_POST['tipo_usuario'] ?? '';

    $stmt = $conn->prepare("SELECT id, senha FROM Usuario WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_usuario, $senha_hash);
        $stmt->fetch();

        if (password_verify($senha, $senha_hash)) {
            $stmt_tipo = $conn->prepare("SELECT id FROM $tipo_selecionado WHERE id_usuario = ?");
            $stmt_tipo->bind_param("i", $id_usuario);
            $stmt_tipo->execute();
            $stmt_tipo->store_result();

            if ($stmt_tipo->num_rows > 0) {
                $_SESSION['id_usuario'] = $id_usuario;
                $_SESSION['tipo'] = $tipo_selecionado;
                $_SESSION['logado'] = true;

                switch ($tipo_selecionado) {
                    case "Cliente":
                        header("Location: index.php");
                        break;
                    case "Loja":
                        header("Location: index_loja.php");
                        break;
                    case "Administrador":
                        header("Location: index_adm.php");
                        break;
                }
                exit();
            } else {
?>
<script>
    alert('Este usuário não é do tipo selecionado!');
    window.history.go(-1);
</script>
<?php
            }
            $stmt_tipo->close();
        } else {
?>
<script>
    alert('Senha incorreta!');
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
} else {
    header("Location: login.html");
    exit();
}
?>