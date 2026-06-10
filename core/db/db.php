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
    die("Error in the database: " . mysqli_connect_error());
}
?>