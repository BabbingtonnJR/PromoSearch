<?php
    $servername = "localhost:3306";
    $username = "root";
    $password = "";
    $dbname = "promosearch";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }
?>