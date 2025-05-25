<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_promocao = $_POST['id'];
    
    $conn->begin_transaction();
    
    try {
        $query_verifica = "SELECT p.id, l.id_usuario 
                          FROM Promocao p
                          JOIN ListaPromocao lp ON p.id = lp.id_promocao
                          JOIN Historico h ON lp.id = h.id_listaPromocao
                          JOIN Loja l ON h.id_loja = l.id
                          WHERE p.id = ? AND l.id_usuario = ?";
        $stmt_verifica = $conn->prepare($query_verifica);
        $stmt_verifica->bind_param("ii", $id_promocao, $_SESSION['id_usuario']);
        $stmt_verifica->execute();
        $result_verifica = $stmt_verifica->get_result();
        
        if ($result_verifica->num_rows === 0) {
            throw new Exception("Promoção não encontrada ou você não tem permissão para excluí-la");
        }

        $query_denuncias = "SELECT id FROM Denuncia WHERE id_promocao = ?";
        $stmt_denuncias = $conn->prepare($query_denuncias);
        $stmt_denuncias->bind_param("i", $id_promocao);
        $stmt_denuncias->execute();
        $result_denuncias = $stmt_denuncias->get_result();
        
        while ($denuncia = $result_denuncias->fetch_assoc()) {
            $query_delete_registro = "DELETE FROM Registro WHERE id_denuncia = ?";
            $stmt_delete_registro = $conn->prepare($query_delete_registro);
            $stmt_delete_registro->bind_param("i", $denuncia['id']);
            $stmt_delete_registro->execute();
        }
        
        $query_delete_denuncias = "DELETE FROM Denuncia WHERE id_promocao = ?";
        $stmt_delete_denuncias = $conn->prepare($query_delete_denuncias);
        $stmt_delete_denuncias->bind_param("i", $id_promocao);
        $stmt_delete_denuncias->execute();
        
        $query_delete_salvas = "DELETE FROM PromocoesSalvas WHERE id_promocao = ?";
        $stmt_delete_salvas = $conn->prepare($query_delete_salvas);
        $stmt_delete_salvas->bind_param("i", $id_promocao);
        $stmt_delete_salvas->execute();
        
        $query_get_listas = "SELECT id FROM ListaPromocao WHERE id_promocao = ?";
        $stmt_get_listas = $conn->prepare($query_get_listas);
        $stmt_get_listas->bind_param("i", $id_promocao);
        $stmt_get_listas->execute();
        $result_listas = $stmt_get_listas->get_result();
        
        while ($lista = $result_listas->fetch_assoc()) {
            $id_lista_promocao = $lista['id'];
            
            $query_delete_historico = "DELETE FROM Historico WHERE id_listaPromocao = ?";
            $stmt_delete_historico = $conn->prepare($query_delete_historico);
            $stmt_delete_historico->bind_param("i", $id_lista_promocao);
            $stmt_delete_historico->execute();
            
            $query_delete_lista = "DELETE FROM ListaPromocao WHERE id = ?";
            $stmt_delete_lista = $conn->prepare($query_delete_lista);
            $stmt_delete_lista->bind_param("i", $id_lista_promocao);
            $stmt_delete_lista->execute();
        }
        
        $query_delete_promocao = "DELETE FROM Promocao WHERE id = ?";
        $stmt_delete_promocao = $conn->prepare($query_delete_promocao);
        $stmt_delete_promocao->bind_param("i", $id_promocao);
        $stmt_delete_promocao->execute();
        
        $conn->commit();
        
        header("Location: produtos.php?success=Promoção excluída com sucesso");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: produtos.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: produtos.php");
    exit();
}
?>