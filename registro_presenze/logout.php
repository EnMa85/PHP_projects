<?php
    session_start();

    // gets the session expired message if exists, or displays the default one
    $message = $_GET['message'] ?? "Disconnessione effettuata.";

    // clears all session data before destroy session
    session_unset();
    session_destroy();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="static/style.css">
    <title>Logout</title>
</head>

<body>
    <header class="login">

    </header>
    
    
    <main>
            <img id="logout" src="static/logout.png" alt="Logout image">

            <p class="login"><?php echo htmlspecialchars($message); ?></p>

            <a class="button" href="index.php">Torna al login</a>
    </main>

    <?php include "_footer.php"; ?>

</body>
</html>
