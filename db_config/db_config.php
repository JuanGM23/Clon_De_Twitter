<?php
$servername = "localhost"; 
$username = "root"; 
$password = "Holajuan23"; 
$database = "social_network"; 

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
