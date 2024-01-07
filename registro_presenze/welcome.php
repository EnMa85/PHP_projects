<?php
    $pageTitle = "Home";
    $pageContent = "welcome.php";
    include "_base.php";
?>

<?php 
//---------------------------------- FIND NEXT LESSON -------------------------------------

    // finds the next lesson of the logged teacher
    $next_lesson = next_lesson($id_teacher);
    // and saves result as html string
    $lesson_message = '';

    // if there's a scheduled lesson
    if ($next_lesson){

        $column = $next_lesson[0];
        $day = $column['giorno'];
        $start = $column['ora_inizio'];
        $end = $column['ora_fine'];
        $course = str_replace('_', ' ', $column['titolo']);

        date_default_timezone_set('Europe/Rome');
        $now = new DateTime();
        $lesson_start = new DateTime("$day $start");
        $lesson_end = new DateTime("$day $end");

        // checks if there's a lesson at the moment
        if (($now >= $lesson_start) && ($now <= $lesson_end)){
            $lesson_message .= "Hai in corso una lezione di<br><br><b>".$course."</b>";
        } else {
            // calculates when the next lesson will be
            $when = $now->diff($lesson_start);
            $days = $when->days;
            $hours = $when->h;
            $minutes = $when->i;

            $lesson_message .= "<b>".$course."</b>";
            if ($days == 0){
                $lesson_message .= "<br><br><br>Oggi tra ";
                $lesson_message .= $hours." ore e ".$minutes." minuti.";
            } else {
                $lesson_message .= "<br><br>tra<br><br>";
                $lesson_message .= $days." giorni, ".$hours." ore e ".$minutes." minuti.";
            }
        }
    } else {
        $lesson_message .= "<br>Non hai lezioni programmate.";
    }
//-------------------------------------------------------------------------------------------
?>

<?php 
//---------------------------------- COURSE STATISTICS ---------------------------------------

    // finds al courses of the logged teacher
    $active_count = count(active_courses($id_teacher));
    $upcoming_count = count(upcoming_courses($id_teacher));
    $history_count = count(course_history($id_teacher));
    // and saves result as html string
    $courses_message = '';

    if($active_count == 0){
        $courses_message .= "Non hai nessun corso attivo";
    } else if($active_count == 1){
        $courses_message .= "Hai <b>1 corso attivo</b>";
    } else {
        $courses_message .= "Hai <b>".$active_count." corsi attivi</b>.";
    }

    if($upcoming_count == 0){
        $courses_message .= "<br><br> Non hai nessun corso in programmazione.";
    } else if($upcoming_count == 1){
        $courses_message .= "<br><br> Hai 1 corso in programmazione.";
    } else {
        $courses_message .= "<br><br> Hai ".$upcoming_count." corsi in programmazione.";
    }

    if($history_count == 1){
        $courses_message .= "<br><br>Hai svolto con noi ".$history_count." corso pregresso.";
    } else {
        $courses_message .= "<br><br>Hai svolto con noi ".$history_count." corsi pregressi.";
    }
//-------------------------------------------------------------------------------------------
?> 


<main class="welcome">
    <p>
        <u>Prossima lezione</u>
        <br><br><br>
        <?= $lesson_message; ?>
        <br>
    </p>

    <img id="welcome" src="static/class.png">

    <p>
        <u>Statistiche corsi</u>
        <br><br><br>
        <?= $courses_message; ?>
    </p>
</main>

<?php include "_footer.php"; ?>