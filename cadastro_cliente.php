<?php
include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $sobrenome = trim($_POST['sobrenome']);
    $endereco = trim($_POST['endereco']);
    $numresidencia = trim($_POST['numresidencia']);
    $cpf = preg_replace('/[^0-9]/', '', trim($_POST['cpf']));
    $telefone = trim($_POST['telefone']);
    $email = trim($_POST['email']);
    $usuario = trim($_POST['usuario']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    
    if ($_POST['senha'] !== $_POST['repetir_senha']) {
?>
<script>
    alert('Erro: As senhas não coincidem.');
    history.go(-1);
</script>
<?php
        exit();
    }
    
    $sql_check = $conn->prepare("SELECT COUNT(*) FROM Usuario WHERE login = ? OR email = ?");
    $sql_check->bind_param("ss", $usuario, $email);
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
        exit();
    }
    
    $sql_check_cpf = $conn->prepare("SELECT COUNT(*) FROM Cliente WHERE cpf = ?");
    $sql_check_cpf->bind_param("s", $cpf);
    $sql_check_cpf->execute();
    $sql_check_cpf->bind_result($count_cpf);
    $sql_check_cpf->fetch();
    $sql_check_cpf->close();

    if ($count_cpf > 0) {
?>
<script>
    alert('Erro: Este CPF já está cadastrado.');
    history.go(-1);
</script>
<?php
        exit();
    }

    $nome_completo = $nome . ' ' . $sobrenome;
    $sql_usuario = $conn->prepare("INSERT INTO Usuario (login, senha, nome, email, telefone, endereco, numero) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $sql_usuario->bind_param("ssssssi", $usuario, $senha, $nome_completo, $email, $telefone, $endereco, $numresidencia);
    
    if ($sql_usuario->execute()) {
        $id_usuario = $conn->insert_id;
        
        $sql_cliente = $conn->prepare("INSERT INTO Cliente (id_usuario, cpf) VALUES (?, ?)");
        $sql_cliente->bind_param("is", $id_usuario, $cpf);
        
        if ($sql_cliente->execute()) {
?>
<script>
    alert('Cadastro realizado com sucesso!');
    location.href = 'login.html';
</script>
<?php
        } else {
?>
<script>
    alert('Erro ao cadastrar cliente!');
    history.go(-1);
</script>
<?php
        }
        
        $sql_cliente->close();
    } else {
?>
<script>
    alert('Erro ao cadastrar usuário!');
    history.go(-1);
</script>
<?php
    }
    
    $sql_usuario->close();
    $conn->close();
}
?>