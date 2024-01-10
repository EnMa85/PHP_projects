<?php

include_once "_connection.php";
include_once "_functions.php";

// sets cookies expiration
session_set_cookie_params ([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => '.localhost',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// starts the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// creates inactivity time counter, if not exists
if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

// if inactive session, redirects to the login page
if (!isset($_SESSION['id_teacher'])) {
    header("Location: index.php");
    exit();
}
   
// disconnects due to inactivity
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    $message = urlencode("Sessione terminata per inattività.");
    header("Location: logout.php?message=$message");
    exit();
}

// sets to the current time the last activity session var
$_SESSION['last_activity'] = time();

$last_activity = $_SESSION['last_activity'];
$teacher = $_SESSION['teacher'];
$id_teacher = $_SESSION['id_teacher'];


// if there's a teacher logged, loads basic page
if ($id_teacher){

    // loads clock
    clock_script();
?>

    <!DOCTYPE html>
    <html lang="IT">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= $pageTitle; ?></title>
            <link rel="stylesheet" href="static/style.css">
            <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        </head>

        <body>
            <div class="nav">
                <a href="welcome.php" class="menu">Home</a>
                <a href="register.php" class="menu">Registro</a>
                <a href="calendar.php" class="menu">Calendario</a>
                <a href="courses.php" class="menu">Corsi</a>
                <a href="assistance.php" class="menu">Assitenza</a>	
                <a href="logout.php" class="menu">Logout</a>	
            </div>

            <br>

            <header>
                <p>Sei loggato come <?= $teacher ?>&ensp;</p>
                <p>Sei su:&ensp;<?=$pageTitle?></p> 
                <p id="now"></p>
            </header>

<?php
            // loads the current page
            include_once $pageContent; 
}
?>
        </body>
    </html>
