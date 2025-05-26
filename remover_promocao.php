<?php
session_start();
include "connection.php";

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$id_promocao = $_POST['id_promocao'] ?? null;

if (!$id_promocao) {
    die("Promoção inválida.");
}

$stmt = $conn->prepare("SELECT id FROM Cliente WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($cliente = $result->fetch_assoc()) {
    $id_cliente = $cliente['id'];

    $delete = $conn->prepare("DELETE FROM PromocoesSalvas WHERE id_promocao = ? AND id_cliente = ?");
    $delete->bind_param("ii", $id_promocao, $id_cliente);
    $delete->execute();
}

$conn->close();

header("Location: promocoes_salvas.php");
exit();
?>
