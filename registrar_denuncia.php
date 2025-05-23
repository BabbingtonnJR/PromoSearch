<?php
session_start();
include "connection.php";

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$descricao = $_POST['descricao'] ?? '';
$id_loja = (int)$_POST['id_loja'] ?? '';

if (!$descricao || !$id_loja) {
    die("Dados incompletos.");
}

$sql_cliente = "SELECT id FROM Cliente WHERE id_usuario = ?";
$stmt = $conn->prepare($sql_cliente);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $id_cliente = $row['id'];
} else {
    die("Usuário não é um cliente.");
}

$sql_promocao = "
    SELECT P.id AS id_promocao
    FROM Historico H
    JOIN ListaPromocao LP ON H.id_listaPromocao = LP.id
    JOIN Promocao P ON LP.id_promocao = P.id
    WHERE H.id_loja = ?
    ORDER BY P.id DESC
    LIMIT 1
";
$stmt = $conn->prepare($sql_promocao);
$stmt->bind_param("i", $id_loja);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $id_promocao = $row['id_promocao'];
} else {
    die("Nenhuma promoção encontrada para esta loja.");
}

$sql_insert = "INSERT INTO Denuncia (id_cliente, id_promocao, descricao, estado, dataDenuncia)
               VALUES (?, ?, ?, 0, CURDATE())";
$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("iis", $id_cliente, $id_promocao, $descricao);
$stmt->execute();

echo "<script>alert('Denúncia registrada com sucesso!'); window.location.href='index.php';</script>";
$conn->close();
?>
