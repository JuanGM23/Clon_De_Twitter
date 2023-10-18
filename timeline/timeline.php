<?php
include('../db_config/db_config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../inicio_sesion/inicio_sesion.php');
    exit();
}

$tweetsPorPagina = 5;

if (isset($_GET['show']) && $_GET['show'] == 'followed') {
    $totalTweetsQuery = "SELECT COUNT(*) FROM publications 
                         INNER JOIN follows ON publications.userId = follows.userToFollowId
                         WHERE follows.users_id = ?";
    $stmtTotalTweets = $conn->prepare($totalTweetsQuery);
    $stmtTotalTweets->bind_param("i", $_SESSION['user_id']);
} else {
    $totalTweetsQuery = "SELECT COUNT(*) FROM publications";
    $stmtTotalTweets = $conn->prepare($totalTweetsQuery);
}
$stmtTotalTweets->execute();
$stmtTotalTweets->bind_result($totalTweets);
$stmtTotalTweets->fetch();
$stmtTotalTweets->close();

$totalPaginas = ceil($totalTweets / $tweetsPorPagina);

$paginaActual = isset($_GET['page']) ? $_GET['page'] : 1;

$indiceInicio = ($paginaActual - 1) * $tweetsPorPagina;
$indiceFin = $indiceInicio + $tweetsPorPagina;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tweet'])) {
    $tweetText = htmlspecialchars($_POST['tweet']);
    $userId = $_SESSION['user_id'];

    $insertTweetQuery = "INSERT INTO publications (userId, text, createDate) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($insertTweetQuery);
    $stmt->bind_param("is", $userId, $tweetText);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['show']) && $_GET['show'] == 'followed') {

    $getTweetsQuery = "SELECT publications.id, publications.text, publications.createDate, users.username, users.id FROM publications 
                   INNER JOIN follows ON publications.userId = follows.userToFollowId
                   INNER JOIN users ON publications.userId = users.id
                   WHERE follows.users_id = ? ORDER BY publications.createDate DESC LIMIT ?, ?";
    $stmt = $conn->prepare($getTweetsQuery);
    $stmt->bind_param("iii", $_SESSION['user_id'], $indiceInicio, $tweetsPorPagina);
} else {

    $getTweetsQuery = "SELECT publications.id, publications.text, publications.createDate, users.username, users.id FROM publications 
                   INNER JOIN users ON publications.userId = users.id
                   ORDER BY publications.createDate DESC LIMIT ?, ?";
    $stmt = $conn->prepare($getTweetsQuery);
    $stmt->bind_param("ii", $indiceInicio, $tweetsPorPagina);
}

$stmt->execute();
$stmt->bind_result($tweetId, $tweetText, $tweetCreateDate, $tweetUsername, $tweetUserId);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <style>
   body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 80%;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 24px;
            color: #1da1f2;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        textarea {
            width: calc(100% - 20px);
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }

        input[type="submit"] {
            background-color: #1da1f2;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0d8bf2;
        }

        .tweet {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: left;
            width: calc(100% - 22px);
            word-wrap: break-word;
            position: relative;
        }

        .tweet strong {
            color: #1da1f2;
        }

        .user-card {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 15px;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-info {
            text-align: left;
        }

        .user-info h3 {
            margin: 0;
            font-size: 18px;
            color: #1da1f2;
        }

        .user-info p {
            margin: 0;
            font-size: 14px;
            color: #555;
        }

        .pagination {
            margin-top: 20px;
        }

        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            color: #1da1f2;
            font-weight: bold;
        }

        .pagination a:hover {
            text-decoration: underline;
        }

        .buttons {
            margin-top: 20px;
        }

        .buttons a {
            text-decoration: none;
            color: #1da1f2;
            font-weight: bold;
            margin: 0 15px;
        }

        .buttons a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
<div class="container">
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>!</h2>
        <div class="buttons">
            <a href="timeline.php">Ver todos los tweets</a>
            |
            <a href="timeline.php?show=followed">Ver tweets de personas a las que sigues</a>
        </div>
        <form method="post" action="">
            <label for="tweet">Nuevo Tweet:</label><br>
            <textarea id="tweet" name="tweet" rows="4" required></textarea><br>
            <input type="submit" value="Enviar Tweet">
        </form>

        <h3>Tweets:</h3>
        <?php
        while ($stmt->fetch()) {
            $isEditable = ($tweetUserId == $_SESSION["user_id"]) ? 'true' : 'false';
            echo "<div class='tweet'><strong><a href='../perfil/perfil.php?editable=$isEditable&user_id=" . htmlspecialchars($tweetUserId) . "'>" . htmlspecialchars($tweetUsername) . "</a></strong> - " . htmlspecialchars($tweetText) . " (Publicado el " . htmlspecialchars($tweetCreateDate) . ")</div>";
        }
        $stmt->close();
        ?>

        <?php if ($totalPaginas > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <a href="timeline.php?page=<?php echo $i; ?>&show=<?php echo htmlspecialchars($_GET['show'] ?? ''); ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

        <a href='../perfil/perfil.php'>Ver perfil</a><br>
        <a href='../cerrar_sesion/cerrar_sesion.php'>Cerrar Sesión</a>

        <?php if (isset($_GET['show']) && $_GET['show'] == 'followed'): ?>
            <h3>Personas a las que sigues:</h3>
            <?php
            $getFollowedUsersQuery = "SELECT users.id, users.username FROM users 
                                      INNER JOIN follows ON users.id = follows.userToFollowId
                                      WHERE follows.users_id = ?";
            $stmtFollowedUsers = $conn->prepare($getFollowedUsersQuery);
            $stmtFollowedUsers->bind_param("i", $_SESSION['user_id']);
            $stmtFollowedUsers->execute();
            $stmtFollowedUsers->bind_result($followedUserId, $followedUsername);
            while ($stmtFollowedUsers->fetch()) {
                echo "<div class='user-card'>
                
                        <div class='user-info'>
                            <h3>" . htmlspecialchars($followedUsername) . "</h3>
                            <p><a href='../perfil/perfil.php?user_id=" . htmlspecialchars($followedUserId) . "'>Ver perfil</a></p>
                        </div>
                    </div>";
            }
            $stmtFollowedUsers->close();
            ?>
        <?php endif; ?>
    </div>
</body>

</html>
