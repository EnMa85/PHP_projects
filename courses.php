<?php
    $pageTitle = "Corsi";
    $pageContent = "courses.php";
    include "_base.php";

    // loads all courses of the logged teacher by period
    $active_courses   = active_courses($id_teacher);
    $upcoming_courses = upcoming_courses($id_teacher);
    $course_history   = course_history($id_teacher);
?>

<main>   

    <?php create_section($active_courses, "CORSI ATTIVI"); ?>

    <?php create_section($upcoming_courses, "CORSI IN PROGRAMMAZIONE"); ?>

    <?php create_section($course_history, "CORSI TERMINATI") ?>
  
</main>

<?php 
    include "_footer.php"; 
?>
   