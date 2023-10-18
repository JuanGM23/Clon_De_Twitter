<?php
include('../db_config/db_config.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password']; 
 
    $sql = "SELECT id, username, password FROM users WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($userId, $dbUsername, $hashContrasena);
        $stmt->fetch();

        if (password_verify($password, $hashContrasena)) {
            $_SESSION['user_id'] = $userId; 
            $_SESSION['username'] = $dbUsername;
            
            $stmt->close();
            header("Location: ../timeline/timeline.php");
            exit();
        }
    }

    $stmt->close();
    header("Location: ../index.php?error=1");
    exit();
}
?>
