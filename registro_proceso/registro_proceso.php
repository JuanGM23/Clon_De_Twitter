<?php
include('../db_config/db_config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $createDate = date("Y-m-d H:i:s");
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password, email, createDate) VALUES ('$username', '$hashedPassword', '$email', '$createDate')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['username'] = $username;
        header("Location: ../timeline/timeline.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>
