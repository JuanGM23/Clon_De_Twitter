<?php
include('../db_config/db_config.php');
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

$editable = false;
$usuarioId = $_SESSION["user_id"];
$seguir = false;

if (isset($_GET['user_id'])) {
    $usuarioId = $_GET['user_id'];
    $editable = ($usuarioId == $_SESSION["user_id"]);

    $consultaSeguir = "SELECT COUNT(*) FROM follows WHERE users_id = ? AND userToFollowId = ?";
    $stmtSeguir = $conn->prepare($consultaSeguir);
    $stmtSeguir->bind_param("ii", $_SESSION['user_id'], $usuarioId);
    $stmtSeguir->execute();
    $stmtSeguir->bind_result($cantidadSeguidores);
    $stmtSeguir->fetch();
    $stmtSeguir->close();

    $seguir = ($cantidadSeguidores > 0);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seguir'])) {
    header("Location: perfil.php?user_id=$usuarioId");
    exit();
}

$consulta = "SELECT username, description, createDate FROM users WHERE id='$usuarioId'";
$resultado = $conn->query($consulta);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Perfil</title>
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

        .separator {
            margin: 20px 0;
            border-top: 1px solid #ccc;
        }

        .date {
            margin-top: 20px;
            font-style: italic;
        }

        a {
            text-decoration: none;
            color: #1da1f2;
            font-weight: bold;
            margin: 10px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            $username = $fila["username"];
            $descripcion = $fila["description"];
            $fechaCreacion = $fila["createDate"];
            echo "<h2>Perfil de $username:</h2>";
            echo "Descripción: " . htmlspecialchars($descripcion) . "<br>";
            echo "<div class='separator'></div>";
            echo "<div class='date'>Fecha de Creación: " . htmlspecialchars($fechaCreacion) . "</div><br>";

            if (!$editable && !$seguir) {
                echo "<form method='post'><input type='submit' name='seguir' value='Seguir'></form>";
            }

            if ($editable) {
                echo "<div class='separator'></div>";
                echo "<a href='../editar_perfil/editar_perfil.php'>Editar Descripción</a>";
            }
        } else {
            echo "No se encontró la descripción del usuario.<br>";
        }
        ?>

        <div class="separator"></div>

        <a href='../timeline/timeline.php'>Volver al Inicio</a>
    </div>
</body>

</html>
