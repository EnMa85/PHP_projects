<?php
    $pageTitle = "Calendario";
    $pageContent = "calendar.php";
    include "_base.php";

    // loads all programmed lessons of the logged teacher
    $calendar = calendar($id_teacher);
?>

<main>

    <?php create_section($calendar, "CALENDARIO LEZIONI"); ?>

</main>
    
<?php include "_footer.php"; ?>