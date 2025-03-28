<?php
include "connection.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST['txtUser']);
    $senha = trim($_POST['txtPsw']);

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
            header("Location: index.php");
            exit();
        } else {
?>
<script>
    alert('Usuário ou senha incorretos!'); 
    window.history.go(-1);
</script>";
<?php
        }
    } else {
?>
<script>
    alert('Usuário não encontrado!');
    window.history.go(-1);
</script>";
<?php
    }
    
    $stmt->close();
    $conn->close();
}
?>
