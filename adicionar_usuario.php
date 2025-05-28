<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'Administrador') {
    header("Location: login.html");
    exit();
}

include "connection.php";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Loja</title>
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

        a {
            font-family: arial;
            text-decoration: none;
            color: white;
        }
    </style>

</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <a href="index_adm.php"><h2>Painel Administrativo</h2></a>
            </div>
            <ul class="nav-links">
                <li class="dropdown">
                    <button class="dropdown-btn">Menu</button>
                    <ul class="dropdown-content">
                        <li><a href="gerenciar_usuarios.php">Gerenciar Usuários</a></li>
                        <li><a href="adicionar_usuario.php">Adicionar Usuário</a></li>
                        <li><a href="visualizar_denuncias.php">Visualizar Denúncias</a></li>
                        <li><a href="historico_penalizacoes.php">Histórico de Penalizações</a></li>
                        <li><a href="logout.php">Sair</a></li>
                    </ul>
                </li>
                <li class="profile">
                    <a href="perfil_adm.php">
                        <img src="https://w7.pngwing.com/pngs/1000/665/png-transparent-computer-icons-profile-s-free-angle-sphere-profile-cliparts-free.png" alt="Perfil Admin">
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="content">
        <h1>Adicionar Nova Loja</h1>
        <div class="add-container">
            <form method="POST" action="cadastro_loja.php">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome_loja">Nome da loja <span class="required">*</span>:</label>
                        <input type="text" id="nome_loja" name="nome_loja" required>
                    </div>
                    <div class="form-group">
                        <label for="proprietario">Nome do Proprietário <span class="required">*</span>:</label>
                        <input type="text" id="proprietario" name="proprietario" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="endereco">Endereço <span class="required">*</span>:</label>
                        <input type="text" id="endereco" name="endereco" required>
                    </div>
                    <div class="form-group">
                        <label for="complemento">Número <span class="required">*</span>:</label>
                        <input type="text" id="complemento" name="numloja" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="cnpj">CNPJ <span class="required">*</span>:</label>
                        <input type="text" id="cnpj" name="cnpj" required>
                    </div>
                    <div class="form-group">
                        <label for="telefone">Telefone <span class="required">*</span>:</label>
                        <input type="text" id="telefone" name="telefone" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">E-mail <span class="required">*</span>:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="usuario">Usuário <span class="required">*</span>:</label>
                        <input type="text" id="usuario" name="usuario" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="senha">Senha <span class="required">*</span>:</label>
                        <input type="password" id="senha" name="senha" required>
                    </div>
                    <div class="form-group">
                        <label for="repetir_senha">Repita a Senha <span class="required">*</span>:</label>
                        <input type="password" id="repetir_senha" name="repetir_senha" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">CADASTRAR</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
if (isset($conn)) {
    $conn->close();
}
?>
