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
    $id_usuario = $_SESSION['id_usuario'];

    $queryLoja = "SELECT id FROM Loja WHERE id_usuario = ?";
    $stmtLoja = mysqli_prepare($conn, $queryLoja);
    mysqli_stmt_bind_param($stmtLoja, "i", $id_usuario);
    mysqli_stmt_execute($stmtLoja);
    $resultLoja = mysqli_stmt_get_result($stmtLoja);

    if ($rowLoja = mysqli_fetch_assoc($resultLoja)) {
        $id_loja = $rowLoja['id'];

        $query = "INSERT INTO Promocao (id_loja, nomeProduto, precoInicial, precoPromocional, quantidade, tipo) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "isddis", $id_loja, $nomeProduto, $precoInicial, $precoPromocional, $quantidade, $tipo);

        if (mysqli_stmt_execute($stmt)) {
            $id_promocao = mysqli_insert_id($conn);

            $queryLista = "INSERT INTO ListaPromocao (id_promocao) VALUES (?)";
            $stmtLista = mysqli_prepare($conn, $queryLista);
            mysqli_stmt_bind_param($stmtLista, "i", $id_promocao);
            
            if (mysqli_stmt_execute($stmtLista)) {
                $id_listaPromocao = mysqli_insert_id($conn);
            
                $queryHistorico = "INSERT INTO Historico (id_listaPromocao, id_loja) VALUES (?, ?)";
                $stmtHistorico = mysqli_prepare($conn, $queryHistorico);
                mysqli_stmt_bind_param($stmtHistorico, "ii", $id_listaPromocao, $id_loja);
                
                if (mysqli_stmt_execute($stmtHistorico)) {
                    header("Location: produtos.php?success=1");
                    exit();
                } else {
                    header("Location: cadastrar_promocao.php?error=historico");
                }
                mysqli_stmt_close($stmtHistorico);
            } else {
                header("Location: cadastrar_promocao.php?error=lista");
            }
            mysqli_stmt_close($stmtLista);
        } else {
            header("Location: cadastrar_promocao.php?error=promocao");
        }
        mysqli_stmt_close($stmt);
    } else {
        header("Location: cadastrar_promocao.php?error=noloja");
    }
    mysqli_stmt_close($stmtLoja);
    mysqli_close($conn);
} else {
    header("Location: cadastrar_promocao.php");
}
?>