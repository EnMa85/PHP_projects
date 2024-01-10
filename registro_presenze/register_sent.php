<?php
$pageTitle = "Invia_registro";
$pageContent = "register_sent.php";
include "_base.php";

if (isset($_POST["send_register"])) {

   $count = 1;
   $student_number = $_POST['student_number'];
   $lesson_id = $_POST['lesson_id'];
   $lesson_end = $_POST['end_lesson'];
   $lesson_status = lesson_status($lesson_id);
   $conn = connect();

   try {
      $conn->begin_transaction();

      // inserts the first lesson state
      $stmt_ins_stat = $conn->prepare("INSERT INTO stato_presenze (id_lezione, id_corsista, stato, orario_variazione) VALUES (?, ?, ?, ?)");
      // updates the presences alredy sents
      $stmt_up_stat = $conn->prepare("UPDATE stato_presenze SET stato = ?, orario_variazione = ? WHERE id_corsista = ? AND id_lezione = ?");

      // inserts a new absence in the register lesson
      $stmt_ins_reg = $conn->prepare("INSERT INTO registro_assenze (id_lezione, id_corsista, ora_inizio, ora_fine, note) VALUES (?, ?, ?, ?, ?)");
      // updates the end time of a previously saved absence
      $stmt_up_reg = $conn->prepare("UPDATE registro_assenze SET ora_fine = ? WHERE id_corsista = ? AND id_lezione = ? AND ora_fine = ?");

      // for every student reads data from post
      while($count <= $student_number){

         $status = $_POST['presence_'.$count];
         $student = $_POST['student_id_'.$count];

         // before saves data, checks if it exists
         if (isset($_POST['time_'.$count])) {
            $time = $_POST['time_'.$count];
        } 
       
         if (isset($_POST['note_'.$count])) {
            $note = $_POST['note_'.$count];
        }

         // for the first sending, all presence/absence status must be saved in the status table
         if (empty($lesson_status)){
            $stmt_ins_stat->bind_param("iiss", $lesson_id, $student, $status, $time);
            $stmt_ins_stat->execute();
            // in case of absence saves also in the class register
            if ($status == 'assente'){
               $stmt_ins_reg->bind_param("iisss", $lesson_id, $student, $time, $lesson_end, $note);
               $stmt_ins_reg->execute();
            }

         // if the first status of presences has already been sent, saves only the variations
         } else {
            if ($status != $lesson_status[$count-1]['stato']){

               // for present to absent variation, saves a new register line
               if($lesson_status[$count-1]['stato'] == 'presente'){
                  $status = 'assente';
                  $stmt_ins_reg->bind_param("iisss", $lesson_id, $student, $time, $lesson_end, $note);
                  $stmt_ins_reg->execute();
               }
               
               // for absent to present variation, modifies end time of the last line
               else {
                  $status = 'presente';
                  $stmt_up_reg->bind_param("siis", $time, $student, $lesson_id, $lesson_end);
                  $stmt_up_reg->execute();
               }

               // for both cases, updates status table
               $stmt_up_stat->bind_param("ssii", $status, $time, $student, $lesson_id);
               $stmt_up_stat->execute();
            }
         }
         // next student
         $count++;
      }

      // transaction commit
      $conn->commit();

      echo "
         <main>
            <p class='login'><br><br><br>Dati inseriti con successo!</p>

            <a class='button' href='register.php'>Torna al registro</a>
         </main>
      ";

   } catch (Exception $e) {
      // rollback in case of error
      $conn->rollback();
      // saves error log
      log_error($e->getMessage());
      echo "
         <main>
            <p><br><br><br>Errore durante l'elaborazione, si prega di riprovare.</p>

            <a class='button' href='register.php'>Torna al registro</a>
         </main>
   ";
  }
   // closes connection and queries
   $stmt_ins_stat->close();
   $stmt_up_stat->close();
   $stmt_ins_reg->close();
   $stmt_up_reg->close();
   $conn->close();
}

include "_footer.php"; 

?>
