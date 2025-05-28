<?php
session_start();


if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'Administrador' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.html");
    exit();
}

include "connection.php";

$denuncia_id   = $_POST['denuncia_id'];
$acao          = $_POST['acao'];
$descricao     = $_POST['descricao'];
$admin_id      = $_SESSION['id_usuario'];

$promocao_id   = isset($_POST['promocao_id']) ? intval($_POST['promocao_id']) : null;
$usuario_id    = isset($_POST['usuario_id']) ? intval($_POST['usuario_id']) : null;
$dias_banimento = isset($_POST['dias_banimento']) ? intval($_POST['dias_banimento']) : null;

$duracao_banimento = null;
if ($acao === 'banimento_temporario' && $dias_banimento > 0) {
    $duracao_banimento = date('Y-m-d', strtotime("+$dias_banimento days"));
}

$conn->begin_transaction();

try {
    $sql_registro = "INSERT INTO Registro (
        id_denuncia,
        id_administrador,
        tipoBanimento,
        descricao,
        dataBanimento,
        duracao
    ) VALUES (?, ?, ?, ?, CURDATE(), ?)";

    $stmt_registro = $conn->prepare($sql_registro);
    $stmt_registro->bind_param("iisss", $denuncia_id, $admin_id, $acao, $descricao, $duracao_banimento);
    $stmt_registro->execute();

    switch ($acao) {
        case 'remover_promocao':
            if ($promocao_id) {
                $stmt_remover = $conn->prepare("DELETE FROM Promocao WHERE id = ?");
                $stmt_remover->bind_param("i", $promocao_id);
                $stmt_remover->execute();
            }
            break;

        case 'ajustar_promocao':
            if ($promocao_id) {
                $stmt_ajustar = $conn->prepare("UPDATE Promocao SET precoPromocional = 99.90 WHERE id = ?");
                $stmt_ajustar->bind_param("i", $promocao_id);
                $stmt_ajustar->execute();
            }
            break;

        case 'banimento_temporario':
        case 'banimento_permanente':
            if ($usuario_id) {
                $banimento_ate = ($acao === 'banimento_permanente') ? '9999-12-31' : $duracao_banimento;
                $stmt_banir = $conn->prepare("UPDATE Usuario SET banido_ate = ? WHERE id = ?");
                $stmt_banir->bind_param("si", $banimento_ate, $usuario_id);
                $stmt_banir->execute();
            }
            break;

        case 'contatar_cliente':
        case 'outra':
            break;
    }

    $stmt_resolver = $conn->prepare("UPDATE Denuncia SET estado = TRUE WHERE id = ?");
    $stmt_resolver->bind_param("i", $denuncia_id);
    $stmt_resolver->execute();

    $conn->commit();
    $_SESSION['mensagem_sucesso'] = "Ação executada com sucesso!";
    header("Location: visualizar_denuncias.php");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['mensagem_erro'] = "Erro ao processar: " . $e->getMessage();
    header("Location: resolver_denuncia.php?id=$denuncia_id");
    exit();
}

$conn->close();
?>
