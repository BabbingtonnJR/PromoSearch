<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

include "connection.php";

$id_usuario = $_SESSION['id_usuario'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomeProduto = $_POST['nomeProduto'];
    $precoInicial = $_POST['precoInicial'];
    $precoPromocional = $_POST['precoPromocional'];
    $quantidade = $_POST['quantidade'];
    $tipo = $_POST['tipo'];

    $sqlLoja = "SELECT id FROM Loja WHERE id_usuario = ?";
    $stmtLoja = $conn->prepare($sqlLoja);
    $stmtLoja->bind_param("i", $id_usuario);
    $stmtLoja->execute();
    $stmtLoja->store_result();

    if ($stmtLoja->num_rows > 0) {
        $stmtLoja->bind_result($id_loja);
        $stmtLoja->fetch();

        $foto_binaria = null;
        $tem_foto = false;

        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $foto_tmp = $_FILES['imagem']['tmp_name'];
            $foto_binaria = file_get_contents($foto_tmp);
            $tem_foto = true;
        } else {
            $foto_binaria = file_get_contents('imagem.png');
        }

        $sqlPromocao = "INSERT INTO Promocao (id_loja, nomeProduto, imagem, precoInicial, precoPromocional, quantidade, tipo) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtPromocao = $conn->prepare($sqlPromocao);
        $stmtPromocao->bind_param("issddis", $id_loja, $nomeProduto, $foto_binaria, $precoInicial, $precoPromocional, $quantidade, $tipo);

        if ($stmtPromocao->execute()) {
            $id_promocao = $stmtPromocao->insert_id;

            $sqlLista = "INSERT INTO ListaPromocao (id_promocao) VALUES (?)";
            $stmtLista = $conn->prepare($sqlLista);
            $stmtLista->bind_param("i", $id_promocao);

            if ($stmtLista->execute()) {
                $id_listaPromocao = $stmtLista->insert_id;

                $sqlHistorico = "INSERT INTO Historico (id_listaPromocao, id_loja) VALUES (?, ?)";
                $stmtHistorico = $conn->prepare($sqlHistorico);
                $stmtHistorico->bind_param("ii", $id_listaPromocao, $id_loja);

                if ($stmtHistorico->execute()) {
                    echo "<script>alert('Promoção cadastrada com sucesso!'); window.location.href='produtos.php';</script>";
                    exit();
                } else {
                    echo "<script>alert('Erro ao cadastrar no histórico'); window.location.href='cadastrar_promocao.php';</script>";
                }

                $stmtHistorico->close();
            } else {
                echo "<script>alert('Erro ao cadastrar na lista de promoções'); window.location.href='cadastrar_promocao.php';</script>";
            }

            $stmtLista->close();
        } else {
            echo "<script>alert('Erro ao cadastrar a promoção'); window.location.href='cadastrar_promocao.php';</script>";
        }

        $stmtPromocao->close();
    } else {
        echo "<script>alert('Loja não encontrada'); window.location.href='cadastrar_promocao.php';</script>";
    }

    $stmtLoja->close();
}

$conn->close();
?>
