<?php
include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_loja = trim($_POST['nome_loja']);
    $proprietario = trim($_POST['proprietario']);
    $endereco = $_POST['endereco'];
    $numloja = trim($_POST['numloja']);
    $cnpj = preg_replace('/[^0-9]/', '', trim($_POST['cnpj']));
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
    
    $sql_check_cnpj = $conn->prepare("SELECT COUNT(*) FROM Loja WHERE cnpj = ?");
    $sql_check_cnpj->bind_param("s", $cnpj);
    $sql_check_cnpj->execute();
    $sql_check_cnpj->bind_result($count_cnpj);
    $sql_check_cnpj->fetch();
    $sql_check_cnpj->close();

    if ($count_cnpj > 0) {
?>
<script>
    alert('Erro: Este CNPJ já está cadastrado.');
    history.go(-1);
</script>
<?php
        exit();
    }

    $sql_usuario = $conn->prepare("INSERT INTO Usuario (login, senha, nome, email, telefone, endereco, numero) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $sql_usuario->bind_param("ssssssi", $usuario, $senha, $nome_loja, $email, $telefone, $endereco, $numloja);
    
    if ($sql_usuario->execute()) {
        $id_usuario = $conn->insert_id;
        
        $sql_loja = $conn->prepare("INSERT INTO Loja (id_usuario, cnpj, proprietario) VALUES (?, ?, ?)");
        $sql_loja->bind_param("iss", $id_usuario, $cnpj, $proprietario);
        
        if ($sql_loja->execute()) {
?>
<script>
    alert('Cadastro realizado com sucesso!');
    location.href = 'login.html';
</script>
<?php
        } else {
?>
<script>
    alert('Erro ao cadastrar loja!');
    history.go(-1);
</script>
<?php
        }
        
        $sql_loja->close();
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