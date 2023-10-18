<?php
include('../db_config/db_config.php');

session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {

    header("Location: ../index.php");
    exit();
}

$userId = $_SESSION['user_id'];
$getUserInfoQuery = "SELECT id, username, email, description FROM users WHERE id = ?";
$stmt = $conn->prepare($getUserInfoQuery);

if (!$stmt) {

    exit("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();

if ($stmt->error) {

    exit("Error al ejecutar la consulta: " . $stmt->error);
}

$stmt->store_result();

if ($stmt->num_rows <= 0) {
    session_unset();
    session_destroy();
    $stmt->close();
    header("Location: ../index.php");
    exit();
}

$stmt->bind_result($userId, $username, $email, $description);
$stmt->fetch();
$stmt->close();
?>
