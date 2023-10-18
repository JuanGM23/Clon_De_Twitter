<?php
include('../db_config/db_config.php');
session_start();

if(!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

$newDescription = mysqli_real_escape_string($conn, $_POST['descripcion']);
$username = $_SESSION['username'];

$sql = "UPDATE users SET description='$newDescription' WHERE username='$username'";

if ($conn->query($sql) === TRUE) {
    header("Location: ../perfil/perfil.php");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
