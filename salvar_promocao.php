<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit();
}

include "connection.php";

$id_usuario = $_SESSION['id_usuario'];
$id_promocao = $_POST['id_promocao'] ?? null;

if (!$id_promocao) {
    echo "Promoção não especificada.";
    exit();
}

$stmt = $conn->prepare("SELECT id FROM Cliente WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Cliente não encontrado.";
    exit();
}
$row = $result->fetch_assoc();
$id_cliente = $row['id'];

$stmt = $conn->prepare("SELECT * FROM PromocoesSalvas WHERE id_promocao = ? AND id_cliente = ?");
$stmt->bind_param("ii", $id_promocao, $id_cliente);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt = $conn->prepare("INSERT INTO PromocoesSalvas (id_promocao, id_cliente) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_promocao, $id_cliente);
    if ($stmt->execute()) {
?>
<script>
    alert("Promoção Salva");
    window.history.go(-1);
</script>
<?php
        exit();
    } else {
?>
<script>
    alert("Erro ao Salvar Promoção.");
    window.history.go(-1);
</script>
<?php
    }
    } else {
?>
<script>
    alert("Promoção ja Salva.");
    window.history.go(-1);
</script>
<?php
}
?>
