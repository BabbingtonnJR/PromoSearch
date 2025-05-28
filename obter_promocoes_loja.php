<?php
session_start();
include 'connection.php';

$nome = $_GET['nome'] ?? '';
$endereco = $_GET['endereco'] ?? '';
$numero = $_GET['numero'] ?? '';
$tipo = $_GET['tipo'] ?? ''; // <-- Novo parâmetro para filtrar por tipo

// Buscar o ID da loja
$query_loja = "SELECT L.id 
               FROM Loja L 
               JOIN Usuario U ON L.id_usuario = U.id 
               WHERE U.nome = ? AND U.endereco = ? AND U.numero = ?";
$stmt_loja = $conn->prepare($query_loja);
$stmt_loja->bind_param("ssi", $nome, $endereco, $numero);
$stmt_loja->execute();
$result_loja = $stmt_loja->get_result();

if ($result_loja->num_rows === 0) {
    echo json_encode(['promocoes' => []]);
    exit();
}

$loja = $result_loja->fetch_assoc();
$id_loja = $loja['id'];

// Consulta de promoções, agora com filtro opcional por tipo
$query_promocoes = "SELECT P.nomeProduto, P.precoInicial, P.precoPromocional, P.quantidade, P.tipo
                   FROM Promocao P
                   JOIN ListaPromocao LP ON P.id = LP.id_promocao
                   JOIN Historico H ON LP.id = H.id_listaPromocao
                   WHERE H.id_loja = ? AND P.quantidade > 0";

if (!empty($tipo)) {
    $query_promocoes .= " AND P.tipo = ?";
}

$stmt_promocoes = $conn->prepare($query_promocoes);

if (!empty($tipo)) {
    $stmt_promocoes->bind_param("is", $id_loja, $tipo); // com filtro de tipo
} else {
    $stmt_promocoes->bind_param("i", $id_loja); // sem filtro de tipo
}

$stmt_promocoes->execute();
$result_promocoes = $stmt_promocoes->get_result();

$promocoes = [];
while ($row = $result_promocoes->fetch_assoc()) {
    $promocoes[] = [
        'nomeProduto' => $row['nomeProduto'],
        'precoInicial' => number_format($row['precoInicial'], 2, ',', '.'),
        'precoPromocional' => number_format($row['precoPromocional'], 2, ',', '.'),
        'quantidade' => $row['quantidade'],
        'tipo' => $row['tipo']
    ];
}

echo json_encode(['promocoes' => $promocoes]);
?>
