<?php
include('../db_config/db_config.php');
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["descripcion"])) {
    $descripcion = mysqli_real_escape_string($conn, $_POST["descripcion"]);
    $descripcion = htmlspecialchars($descripcion);

    $usuarioId = $_SESSION["user_id"];

    $actualizarDescripcion = "UPDATE users SET description='$descripcion' WHERE id='$usuarioId'";

    if ($conn->query($actualizarDescripcion) === TRUE) {
        header("Location: ../perfil/perfil.php");
        exit();
    } else {
        echo "Error al actualizar la descripción: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Editar Perfil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #1da1f2;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #1da1f2;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0d8bf2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Descripción</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            Nueva Descripción: <input type="text" name="descripcion" required><br>
            <input type="submit" value="Guardar">
        </form>
    </div>
</body>
</html>
