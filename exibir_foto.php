<?php
include "connection.php";
session_start();

$id = $_SESSION['id_usuario'];
$sql = "SELECT foto FROM Usuario WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($foto);
$stmt->fetch();
$stmt->close();
$conn->close();

if ($foto) {
    header("Content-Type: image/jpeg");
    echo $foto;
} else {
    readfile("default_profile.jpg");
}
?>

