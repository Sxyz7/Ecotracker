<?php
$host = "127.0.0.1";
$port = 3307;
$usuario = "root";          // MySQL user
$senha = "";                // MySQL password
$banco = "eco";

$conn = new mysqli($host, $usuario, $senha, $banco, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
