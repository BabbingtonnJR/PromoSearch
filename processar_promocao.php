<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomeProduto = $_POST['nomeProduto'];
    $precoInicial = $_POST['precoInicial'];
    $precoPromocional = $_POST['precoPromocional'];
    $quantidade = $_POST['quantidade'];
    $tipo = $_POST['tipo'];
    
    $query = "INSERT INTO Promocao (nomeProduto, precoInicial, precoPromocional, quantidade, tipo) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sddis", $nomeProduto, $precoInicial, $precoPromocional, $quantidade, $tipo);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: produtos.php?success=1");
    } else {
        header("Location: cadastrar_promocao.php?error=1");
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
} else {
    header("Location: cadastrar_promocao.php");
}
?>