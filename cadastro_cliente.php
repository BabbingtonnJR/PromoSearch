<?php
include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $sobrenome = trim($_POST['sobrenome']);
    $endereco = trim($_POST['endereco']);
    $numresidencia = trim($_POST['numresidencia']);
    $cpf = trim($_POST['cpf']);
    $telefone = trim($_POST['telefone']);
    $email = trim($_POST['email']);
    $login = trim($_POST['usuario']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    
    $sql_check = $conn->prepare("SELECT COUNT(*) FROM Cliente WHERE login = ? OR email = ?");
    $sql_check->bind_param("ss", $login, $email);
    $sql_check->execute();
    $sql_check->bind_result($count);
    $sql_check->fetch();
    $sql_check->close();

    if ($count > 0) {

?>
<script>
    alert('Erro: O usuário ou e-mail já está sendo usado.');
    history.go(-1);
</script>
<?php

    } else {
        $sql = $conn->prepare("INSERT INTO Cliente (nome, sobrenome, endereco, numero_residencia, cpf, telefone, email, login, senha) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $sql->bind_param("sssssssss", $nome, $sobrenome, $endereco, $numresidencia, $cpf, $telefone, $email, $login, $senha);
        
        if ($sql->execute()) {
?>
<script>
    alert('Cadastro realizado com sucesso!');
    location.href = 'login.html';
</script>
<?php
        } else { 
?>
<script>
    alert('Erro no cadastro!');
    history.go(-1);
</script>
<?php
        }

        $sql->close();
    }
    
    $conn->close();
}
?>

