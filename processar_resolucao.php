<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 'Administrador' || $_SERVER['REQUEST_METHOD'] !== 'GET') {
    header("Location: login.html");
    exit();
}

include "connection.php";

$denuncia_id = $_POST['denuncia_id'];
$acao = $_POST['acao'];
$descricao = $_POST['descricao'];
$admin_id = $_SESSION['id_usuario'];

$promocao_id = isset($_POST['promocao_id']) ? $_POST['promocao_id'] : null;
$usuario_id = isset($_POST['usuario_id']) ? $_POST['usuario_id'] : null;
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
    $stmt_registro->bind_param(
        "iisss",
        $denuncia_id,
        $admin_id,
        $acao,
        $descricao,
        $duracao_banimento
    );
    $stmt_registro->execute();

    switch ($acao) {
        case 'remover_promocao':
            if ($promocao_id) {
                $conn->query("DELETE FROM Promocao WHERE id = $promocao_id");
            }
            break;

        case 'ajustar_promocao':
            if ($promocao_id) {
                $conn->query("UPDATE Promocao SET precoPromocional = 99.90 WHERE id = $promocao_id");
            }
            break;

        case 'banimento_temporario':
        case 'banimento_permanente':
            if ($usuario_id) {
                $banimento_ate = ($acao === 'banimento_permanente') ? '9999-12-31' : $duracao_banimento;
                $conn->query("UPDATE Usuario SET banido_ate = '$banimento_ate' WHERE id = $usuario_id");
            }
            break;

        case 'contatar_cliente':
            break;
    }

    $conn->query("UPDATE Denuncia SET estado = TRUE WHERE id = $denuncia_id");

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