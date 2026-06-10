<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "fakeInstagram";

$conn = mysqli_connect(
    $host,
    $username,
    $password,
    $database
);

if (!$conn){
    die("Error de conexión a la base de datos");
}

mysqli_set_charset($conn, 'utf8mb4');
?>