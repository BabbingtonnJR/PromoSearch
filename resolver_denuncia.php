<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'Administrador') {
    header("Location: login.html");
    exit();
}

include "connection.php";


if (!isset($_GET['id'])) {
    header("Location: visualizar_denuncias.php");
    exit();
}

$denuncia_id = $_GET['id'];

$sql = "SELECT 
            d.id AS denuncia_id,
            d.descricao AS denuncia_desc,
            d.estado,
            d.dataDenuncia,
            u.nome AS cliente_nome,
            u.email AS cliente_email,
            u.id AS usuario_id,
            p.nomeProduto,
            p.precoPromocional,
            p.id AS promocao_id
        FROM Denuncia d
        JOIN Cliente c ON d.id_cliente = c.id
        JOIN Usuario u ON c.id_usuario = u.id
        JOIN Promocao p ON d.id_promocao = p.id
        WHERE d.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $denuncia_id);
$stmt->execute();
$result = $stmt->get_result();
$denuncia = $result->fetch_assoc();

if (!$denuncia) {
    header("Location: visualizar_denuncias.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resolver Denúncia</title>
    <link rel="stylesheet" href="styles_adm.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <h2>Sistema Admin</h2>
            </div>
            <ul class="nav-links">
                <li><a href="index_adm.php">Início</a></li>
                <li><a href="visualizar_denuncias.php">Denúncias</a></li>
                <li class="profile">
                    <a href="perfil_admin.php">
                        <img src="https://w7.pngwing.com/pngs/1000/665/png-transparent-computer-icons-profile-s-free-angle-sphere-profile-cliparts-free.png" alt="Perfil Admin">
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="content">
        <h1>Resolver Denúncia #<?= htmlspecialchars($denuncia['denuncia_id']) ?></h1>
        
        <div class="denuncia-detalhes">
            <div class="card">
                <h3>Informações da Denúncia</h3>
                <div class="info-row">
                    <span class="label">Data:</span>
                    <span class="value"><?= htmlspecialchars($denuncia['dataDenuncia']) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Cliente:</span>
                    <span class="value">
                        <?= htmlspecialchars($denuncia['cliente_nome']) ?><br>
                        <small><?= htmlspecialchars($denuncia['cliente_email']) ?></small>
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">Produto:</span>
                    <span class="value"><?= htmlspecialchars($denuncia['nomeProduto']) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Preço Promocional:</span>
                    <span class="value">R$ <?= number_format($denuncia['precoPromocional'], 2, ',', '.') ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Descrição:</span>
                    <span class="value"><?= htmlspecialchars($denuncia['denuncia_desc']) ?></span>
                </div>
            </div>
            
            <form action="processar_resolucao.php" method="post" class="resolver-form">
                <input type="hidden" name="denuncia_id" value="<?= $denuncia['denuncia_id'] ?>">
                <input type="hidden" name="promocao_id" value="<?= $denuncia['promocao_id'] ?>">
                <input type="hidden" name="usuario_id" value="<?= $denuncia['usuario_id'] ?>">
                
                <h3>Ação de Resolução</h3>
                
                <div class="form-group">
                    <label for="acao">Tipo de Ação:</label>
                    <select name="acao" id="acao" required>
                        <option value="">Selecione uma ação...</option>
                        <option value="remover_promocao">Remover Promoção</option>
                        <option value="ajustar_promocao">Ajustar Promoção</option>
                        <option value="banimento_temporario">Banimento Temporário</option>
                        <option value="banimento_permanente">Banimento Permanente</option>
                        <option value="contatar_cliente">Contatar Cliente</option>
                        <option value="outra">Outra Ação</option>
                    </select>
                </div>

                <div id="banimento-group" style="display:none; margin-top: 10px;">
                    <label for="dias_banimento">Duração do Banimento (dias):</label>
                    <input 
                        type="number" 
                        name="dias_banimento" 
                        id="dias_banimento" 
                        min="1" 
                        class="form-input"
                        placeholder="Ex: 30 para 1 mês"
                    >
                </div>
                
                <div class="form-group">
                    <label for="descricao">Descrição da Resolução:</label>
                    <textarea name="descricao" id="descricao" rows="5" required placeholder="Descreva as ações tomadas para resolver esta denúncia..."></textarea>
                </div>
                
                <div class="form-actions">
                    <a href="visualizar_denuncias.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Confirmar Resolução</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('acao').addEventListener('change', function() {
            const banimentoGroup = document.getElementById('banimento-group');
            banimentoGroup.style.display = 
                (this.value === 'banimento_temporario') ? 'block' : 'none';
        });
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>