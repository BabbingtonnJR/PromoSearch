<?php
include "connection.php";

$endereco = $_GET['endereco'] ?? '';
$numero = $_GET['numero'] ?? '';

$sql = "SELECT L.id AS id_loja 
        FROM Loja L 
        JOIN Usuario U ON L.id_usuario = U.id 
        WHERE U.endereco = ? AND U.numero = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $endereco, $numero);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["id_loja" => $row["id_loja"]]);
} else {
    echo json_encode(null);
}

$conn->close();
?>
