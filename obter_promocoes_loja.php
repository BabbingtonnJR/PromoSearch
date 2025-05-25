<?php
session_start();
include 'connection.php';

$nome = $_GET['nome'] ?? '';
$endereco = $_GET['endereco'] ?? '';
$numero = $_GET['numero'] ?? '';

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

$query_promocoes = "SELECT P.nomeProduto, P.precoInicial, P.precoPromocional, P.quantidade
                   FROM Promocao P
                   JOIN ListaPromocao LP ON P.id = LP.id_promocao
                   JOIN Historico H ON LP.id = H.id_listaPromocao
                   WHERE H.id_loja = ? AND P.quantidade > 0";
$stmt_promocoes = $conn->prepare($query_promocoes);
$stmt_promocoes->bind_param("i", $id_loja);
$stmt_promocoes->execute();
$result_promocoes = $stmt_promocoes->get_result();

$promocoes = [];
while ($row = $result_promocoes->fetch_assoc()) {
    $promocoes[] = [
        'nomeProduto' => $row['nomeProduto'],
        'precoInicial' => number_format($row['precoInicial'], 2, ',', '.'),
        'precoPromocional' => number_format($row['precoPromocional'], 2, ',', '.'),
        'quantidade' => $row['quantidade']
    ];
}

echo json_encode(['promocoes' => $promocoes]);
?>