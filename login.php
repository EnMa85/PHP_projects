<?php
include "connection.php";

// set cookie expiration
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => '.localhost',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// start output buffer and session
ob_start();
session_start();
session_regenerate_id(true);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="static/style.css">
    <title>Login</title>
</head>

<body>
    <header class="login">
        <h2>Gestione presenze</h2>
    </header>

    <main>
        <img id="login" src="static/login1.png" alt="Login image">
        <p class="login">Inserisci credenziali</p>

        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>" method="post">
            <div>
                <label class="input" for="email">Inserisci nome utente:&emsp;</label>
                <input class="input" type="email" id="email" name="email" required
                       value="<?= htmlspecialchars(isset($_POST['email']) ? $_POST['email'] : '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div>
                <label class="input" for="password">&emsp;Inserisci password:&emsp;</label>
                <input class="input" type="password" id="password" name="password" minlength="4" required>
            </div>
            <input class="button" type="submit" value="&nbsp;Accedi&nbsp;">
        </form>

        <?php

        // check input data and allow access or print exception message
        if ($_POST){
            $email = $_POST['email'];
            $password = $_POST['password'];

            if($email && $password){
                $teacher_data = [];
                $login_result = check_login($email, $password, $teacher_data);

                if ($login_result === true){
                    $_SESSION['last_activity'] = time();
                    $_SESSION['teacher'] = $teacher_data['teacher'];
                    $_SESSION['id_teacher'] = $teacher_data['id_teacher'];
                    header("Location: welcome.php");
                    exit();

                } else {
                    echo "<p>".htmlspecialchars($login_result)."</p>";
                }
            }
        }

        ?>
        
    </main>

    <?php 
        include "_footer.php"; 

        //end output buffer
        ob_end_flush();
    ?>
</body>
</html>