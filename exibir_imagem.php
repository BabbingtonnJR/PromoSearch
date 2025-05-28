<?php
include "connection.php";
session_start();

if (isset($_GET['id'])) {
    $id_promocao = $_GET['id'];

    $sql = "SELECT imagem FROM Promocao WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_promocao);
    $stmt->execute();
    $stmt->bind_result($imagem);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    if ($imagem) {
        header("Content-Type: image/jpeg");
        echo $imagem;
    } else {
        readfile("imagem.png");
    }
} else {
    readfile("imagem.png");
}
?>
