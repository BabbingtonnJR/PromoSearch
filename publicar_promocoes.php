<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

include 'connection.php';

$id_usuario = $_SESSION['id_usuario'];
$query_loja = "SELECT id FROM Loja WHERE id_usuario = ?";
$stmt_loja = $conn->prepare($query_loja);
$stmt_loja->bind_param("i", $id_usuario);
$stmt_loja->execute();
$result_loja = $stmt_loja->get_result();

if ($result_loja->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Loja não encontrada']);
    exit();
}

$loja = $result_loja->fetch_assoc();
$id_loja = $loja['id'];

$data = json_decode(file_get_contents('php://input'), true);
$promocoes = $data['promocoes'] ?? [];

if (empty($promocoes)) {
    echo json_encode(['success' => false, 'message' => 'Nenhuma promoção selecionada']);
    exit();
}

if (count($promocoes) > 3) {
    echo json_encode(['success' => false, 'message' => 'Máximo de 3 promoções permitidas']);
    exit();
}

$conn->begin_transaction();

try {
    $query_delete = "DELETE FROM Historico WHERE id_loja = ?";
    $stmt_delete = $conn->prepare($query_delete);
    $stmt_delete->bind_param("i", $id_loja);
    $stmt_delete->execute();
    
    foreach ($promocoes as $id_promocao) {
        $query_verifica = "SELECT id FROM Promocao WHERE id = ? AND quantidade > 0";
        $stmt_verifica = $conn->prepare($query_verifica);
        $stmt_verifica->bind_param("i", $id_promocao);
        $stmt_verifica->execute();
        $result_verifica = $stmt_verifica->get_result();
        
        if ($result_verifica->num_rows === 0) {
            throw new Exception("Promoção não encontrada ou sem estoque: $id_promocao");
        }
        
        $query_lista = "INSERT INTO ListaPromocao (id_promocao) VALUES (?)";
        $stmt_lista = $conn->prepare($query_lista);
        $stmt_lista->bind_param("i", $id_promocao);
        $stmt_lista->execute();
        $id_lista = $conn->insert_id;
        
        $query_historico = "INSERT INTO Historico (id_listaPromocao, id_loja) VALUES (?, ?)";
        $stmt_historico = $conn->prepare($query_historico);
        $stmt_historico->bind_param("ii", $id_lista, $id_loja);
        $stmt_historico->execute();
    }
    
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>