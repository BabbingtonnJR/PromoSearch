<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'Administrador') {
    header("Location: login.html");
    exit();
}

include "connection.php";

$error = '';
$success = '';
$dados = [
    'tipo' => '',
    'nome' => '',
    'sobrenome' => '',
    'endereco' => '',
    'numresidencia' => '',
    'cpf' => '',
    'usuario' => '',
    'telefone' => '',
    'email' => '',
    'nome_loja' => '',
    'proprietario' => '',
    'numloja' => '',
    'cnpj' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        $dados[$key] = trim($value);
    }
    
    $tipo = $dados['tipo'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if (empty($tipo)) {
        $error = "Selecione o tipo de usuário.";
    } elseif ($senha !== $confirmar_senha) {
        $error = "As senhas não coincidem.";
    } elseif (strlen($senha) < 6) {
        $error = "A senha deve ter pelo menos 6 caracteres.";
    } else {
        if ($tipo === 'Cliente') {
            if (empty($dados['nome']) || empty($dados['sobrenome']) || empty($dados['cpf'])) {
                $error = "Para cliente, nome, sobrenome e CPF são obrigatórios.";
            }
        } elseif ($tipo === 'Loja') {
            if (empty($dados['nome_loja']) || empty($dados['proprietario']) || empty($dados['cnpj'])) {
                $error = "Para loja, nome da loja, proprietário e CNPJ são obrigatórios.";
            }
        }

        if (empty($error)) {
            $check_sql = "SELECT id FROM Usuario WHERE login = ? OR email = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ss", $dados['usuario'], $dados['email']);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $error = "Login ou email já estão em uso.";
            } else {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

                $conn->begin_transaction();

                try {
                    $insert_sql = "INSERT INTO Usuario (nome, login, email, senha, tipo) VALUES (?, ?, ?, ?, ?)";
                    $insert_stmt = $conn->prepare($insert_sql);
                    
                    $nome_completo = ($tipo === 'Cliente') ? $dados['nome'] . ' ' . $dados['sobrenome'] : $dados['nome_loja'];
                    
                    $insert_stmt->bind_param("sssss", $nome_completo, $dados['usuario'], $dados['email'], $senha_hash, $tipo);
                    $insert_stmt->execute();
                    $user_id = $conn->insert_id;

                    if ($tipo === 'Cliente') {
                        $type_sql = "INSERT INTO Cliente (id_usuario, cpf, telefone, endereco, numero_residencia) VALUES (?, ?, ?, ?, ?)";
                        $type_stmt = $conn->prepare($type_sql);
                        $type_stmt->bind_param("issss", $user_id, $dados['cpf'], $dados['telefone'], $dados['endereco'], $dados['numresidencia']);
                    } elseif ($tipo === 'Loja') {
                        $type_sql = "INSERT INTO Loja (id_usuario, cnpj, telefone, endereco, numero_loja, proprietario) VALUES (?, ?, ?, ?, ?, ?)";
                        $type_stmt = $conn->prepare($type_sql);
                        $type_stmt->bind_param("isssss", $user_id, $dados['cnpj'], $dados['telefone'], $dados['endereco'], $dados['numloja'], $dados['proprietario']);
                    } elseif ($tipo === 'Administrador') {
                        $type_sql = "INSERT INTO Administrador (id_usuario) VALUES (?)";
                        $type_stmt = $conn->prepare($type_sql);
                        $type_stmt->bind_param("i", $user_id);
                    }
                    
                    $type_stmt->execute();

                    $conn->commit();

                    $_SESSION['message'] = "Usuário cadastrado com sucesso!";
                    header("Location: gerenciar_usuarios.php");
                    exit();
                } catch (Exception $e) {
                    $conn->rollback();
                    $error = "Erro ao cadastrar usuário: " . $e->getMessage();
                }
            }
            $check_stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Usuário</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .add-container {
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .form-row > div {
            flex: 1;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            border: none;
        }
        
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #45a049;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .error-message {
            color: #dc3545;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }
        
        .tipo-section {
            display: none;
        }
        
        .tipo-section.active {
            display: block;
        }
        
        span.required {
            color: red;
        }
    </style>
    <script>
        function mostrarCamposPorTipo() {
            const tipo = document.getElementById('tipo').value;
            document.querySelectorAll('.tipo-section').forEach(section => {
                section.classList.remove('active');
            });
            
            if (tipo) {
                document.getElementById(`campos-${tipo.toLowerCase()}`).classList.add('active');
            }
        }
    </script>
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
        <h1>Adicionar Novo Usuário</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="add-container">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="tipo">Tipo de Usuário<span class="required">*</span>:</label>
                    <select id="tipo" name="tipo" required onchange="mostrarCamposPorTipo()">
                        <option value="">Selecione um tipo</option>
                        <option value="Cliente" <?= $dados['tipo'] === 'Cliente' ? 'selected' : '' ?>>Cliente</option>
                        <option value="Loja" <?= $dados['tipo'] === 'Loja' ? 'selected' : '' ?>>Loja</option>
                        <option value="Administrador" <?= $dados['tipo'] === 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <div class="form-row">
                        <div>
                            <label for="usuario">Usuário<span class="required">*</span>:</label>
                            <input type="text" id="usuario" name="usuario" value="<?= htmlspecialchars($dados['usuario']) ?>" required>
                        </div>
                        <div>
                            <label for="email">E-Mail<span class="required">*</span>:</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($dados['email']) ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="form-row">
                        <div>
                            <label for="senha">Senha<span class="required">*</span>:</label>
                            <input type="password" id="senha" name="senha" required>
                        </div>
                        <div>
                            <label for="confirmar_senha">Confirmar Senha<span class="required">*</span>:</label>
                            <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                        </div>
                    </div>
                </div>

                <div id="campos-cliente" class="tipo-section <?= $dados['tipo'] === 'Cliente' ? 'active' : '' ?>">
                    <h3>Dados do Cliente</h3>
                    <div class="form-group">
                        <div class="form-row">
                            <div>
                                <label for="nome">Nome<span class="required">*</span>:</label>
                                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($dados['nome']) ?>">
                            </div>
                            <div>
                                <label for="sobrenome">Sobrenome<span class="required">*</span>:</label>
                                <input type="text" id="sobrenome" name="sobrenome" value="<?= htmlspecialchars($dados['sobrenome']) ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <div>
                                <label for="cpf">CPF<span class="required">*</span>:</label>
                                <input type="text" id="cpf" name="cpf" value="<?= htmlspecialchars($dados['cpf']) ?>">
                            </div>
                            <div>
                                <label for="telefone">Telefone<span class="required">*</span>:</label>
                                <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($dados['telefone']) ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <div>
                                <label for="endereco">Endereço<span class="required">*</span>:</label>
                                <input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($dados['endereco']) ?>">
                            </div>
                            <div>
                                <label for="numresidencia">Número Residencial<span class="required">*</span>:</label>
                                <input type="text" id="numresidencia" name="numresidencia" value="<?= htmlspecialchars($dados['numresidencia']) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="campos-loja" class="tipo-section <?= $dados['tipo'] === 'Loja' ? 'active' : '' ?>">
                    <h3>Dados da Loja</h3>
                    <div class="form-group">
                        <div class="form-row">
                            <div>
                                <label for="nome_loja">Nome da Loja<span class="required">*</span>:</label>
                                <input type="text" id="nome_loja" name="nome_loja" value="<?= htmlspecialchars($dados['nome_loja']) ?>">
                            </div>
                            <div>
                                <label for="proprietario">Proprietário<span class="required">*</span>:</label>
                                <input type="text" id="proprietario" name="proprietario" value="<?= htmlspecialchars($dados['proprietario']) ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <div>
                                <label for="cnpj">CNPJ<span class="required">*</span>:</label>
                                <input type="text" id="cnpj" name="cnpj" value="<?= htmlspecialchars($dados['cnpj']) ?>">
                            </div>
                            <div>
                                <label for="telefone">Telefone<span class="required">*</span>:</label>
                                <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($dados['telefone']) ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <div>
                                <label for="endereco">Endereço<span class="required">*</span>:</label>
                                <input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($dados['endereco']) ?>">
                            </div>
                            <div>
                                <label for="numloja">Número<span class="required">*</span>:</label>
                                <input type="text" id="numloja" name="numloja" value="<?= htmlspecialchars($dados['numloja']) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="campos-administrador" class="tipo-section <?= $dados['tipo'] === 'Administrador' ? 'active' : '' ?>">
                    <h3>Dados do Administrador</h3>
                    <div class="form-group">
                        <div class="form-row">
                            <div>
                                <label for="nome">Nome Completo<span class="required">*</span>:</label>
                                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($dados['nome']) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="gerenciar_usuarios.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Cadastrar Usuário</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            mostrarCamposPorTipo();
        });
    </script>
</body>
</html>

<?php
if (isset($conn)) {
    $conn->close();
}
?>