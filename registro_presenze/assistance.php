<?php
    $pageTitle = "Assistenza";
    $pageContent = "assistance.php";
    include "_base.php";

    // if submits, processes data
    if (isset($_POST["subject"]) && isset($_POST["message"])){
       
        $subject = $_POST["subject"];
        $message = $_POST["message"];
        $sender_mail = get_email($_SESSION['id_teacher']);
        $sender_name = $_SESSION['teacher'];

        echo "<main>";
            // sends email and prints confirms or relative error message
            $mail_sent = send_email($subject, $message, $sender_mail, $sender_name);

            if ($mail_sent === true){
                echo '<p> <br><br> Email inviata con successo! <br><br> </p>';
                echo '<a class="button" href="welcome.php"> Torna alla home </a>';
            } else {
                echo '<p> <br><br> '.htmlspecialchars($mail_sent).' <br><br> </p>';
                echo '<a class="button" href="assistance.php"> Torna al form di contatto </a>';
            }
        echo "</main>";

    } else {
        
    //if not submits, loads form page
?>

<main>
    <p>Chiedi assistenza</p>

    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>" method="post">
        <div>
            <textarea class="input" id="subject" name="subject" rows="1" placeholder="Inserisci qui l'oggetto: " required></textarea>
        </div>

        <div>
            <textarea class="input" id="message" name="message" rows="7" placeholder="Inserisci qui il tuo messaggio: " required></textarea>
        </div>

        <button class="button" type="submit" name="submit">Invia messaggio</button>
    </form>
</main>

<?php } ?>

<?php include "_footer.php"; ?>
