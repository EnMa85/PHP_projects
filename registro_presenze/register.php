User
<?php
$pageTitle = "Registro";
$pageContent = "register.php";
include "_base.php";

$today = date('d-m-Y');
$now = date('H:i');
$next_lesson = next_lesson($id_teacher);
$active_lesson = false;

// checks if there's a lesson right now
if ($next_lesson &&
    $next_lesson[0]['giorno'] == $today && 
    $next_lesson[0]['ora_inizio'] <= $now && 
    $next_lesson[0]['ora_fine'] > $now
){
    $active_lesson = true;

    // student list of this course
    $student_list = student_list($next_lesson[0]['id_corso']);
    // status of presences of this lesson
    $lesson_status = lesson_status($next_lesson[0]['id']);
    // register of this lesson
    $lesson_register = lesson_register($next_lesson[0]['id']);

    // presences not yet sent for this lesson
    $first_sending = empty($lesson_status);
    
    // find hour of last update of register, if already sent for this lesson
    $last_register_update = ($first_sending) ? $next_lesson[0]['ora_inizio'] : max(array_column($lesson_status, 'orario_variazione'));

    // aggregates course data to send it to the HTML page
    $course_data = [
        'id' => $next_lesson[0]['id'],
        'course_id' => $next_lesson[0]['id_corso'],
        'course_title' => str_ireplace("_", " ", $next_lesson[0]['titolo']),
        'lesson_id' => $next_lesson[0]['id'],
        'lesson_day' => $next_lesson[0]['giorno'],
        'lesson_start' => $next_lesson[0]['ora_inizio'],
        'lesson_end' => $next_lesson[0]['ora_fine'],
        'last_update' => $last_register_update
    ];
    
    $students_data = [];
    
    foreach($student_list as $current => $student){    
    
        // presence not yet sent or the student is present
        $checked = ($first_sending || $lesson_status[$current]['stato'] == 'presente');

        // prepares the check based on previous status
        $present_checked = ($checked) ? 'checked' : '';
        $absent_checked = ($checked) ? '' : 'checked';

        // if it's the first sending, time must coincides with start lesson hour
        if ($first_sending) {
            $attribute = 'readonly';
            $last_student_update = $next_lesson[0]['ora_inizio'];

        // for next updates, time will be editables only on checkbox variations
        } else {
            $attribute = 'disabled';
            $last_student_update = substr($lesson_status[$current]['orario_variazione'], 0, 5);
        }

        // iterates all student and finds, for every absent, the last note registered
        $note = "";
        foreach ($lesson_register as $key => $value) {
            if (($value['id_corsista'] == $student['id']) && ($lesson_status[$current]['stato'] == 'assente')){
                $note = $value['note'];
            }
        }
        
    // aggregates single student data
        $student_data = [
            'id' => $student['id'],
            'surname' => $student['cognome'],
            'name' => $student['nome'],
            'cf' => $student['cf'],
            'presence' => $present_checked,
            'absence' => $absent_checked,
            'time_attribute' => $attribute,
            'time' => $last_student_update,
            'note' => $note
        ];

        // appends single student data as row in a multidimensional array
        $students_data[] = $student_data;
    }
}
?>


<main>
   
<?php
if ($active_lesson){
    // ables and disables fields based on presence checkbox variations
    set_input_field_script();
?>

    <div class="table">
        <p class="table">Corso: <b><?= $course_data['course_id']." - ".$course_data['course_title'] ?></b></p>
        <p class="table">Lezione del: <b><?= $course_data['lesson_day'] ?></b></p>
        <p class="table">Fascia oraria: <b><?= $course_data['lesson_start']." - ".$course_data['lesson_end'] ?></b></p>
    </div>
    
        <form action="register_sent.php" method="post">
    
            <table>
                <tr height="30px">
                    <th width="5%">&thinsp;N.</th>
                    <th width="auto">Cognome</th>
                    <th width="auto">Nome</th>
                    <th width="15%">Codice fiscale</th>
                    <th width="auto">Presente</th>
                    <th width="9%">Da ore</th>
                    <th width="30%">Note</th>
                </tr>

<?php
    $current = 0;
    foreach ($students_data as $student){
        $current++;
?>
            <input type='hidden' name='student_id_<?=$current?>' value='<?=$student['id']?>'>
                <tr>
                    <td><?=$current?></td>
                    <td><?=$student['surname']?></td>
                    <td><?=$student['name']?></td>
                    <td><?=$student['cf']?></td>
                    <td>
                        <label for='present'>Si</label>  
                        <input type='radio' id='present' name='presence_<?=$current?>' value='presente' 
                               <?=$student['presence']?> onchange='set_input_field(this)'>
                        <label for='absent'>&ensp;No</label>  
                        <input type='radio' id='absent' name='presence_<?=$current?>' value='assente' 
                               <?=$student['absence']?> onchange='set_input_field(this)'>
                    </td>
                    <td>
                        <input type='time' id='time' name='time_<?=$current?>' <?=$student['time_attribute']?>
                               value='<?=(isset($_POST['time_'.$current]) ? htmlspecialchars($_POST['time_'.$current], ENT_QUOTES, 'UTF-8') : substr($student['time'], 0, 5))?>'
                               min='<?=htmlspecialchars(date('H:i', strtotime($course_data['last_update'])), ENT_QUOTES, 'UTF-8')."' max='".htmlspecialchars(date('H:i'), ENT_QUOTES, 'UTF-8')?>'>             
                    </td>
                    <td>
                        <input type='text' id='note' name='note_<?=$current?>' minlength='3' required disabled
                               value='<?=(isset($_POST['note_'.$current]) ? htmlspecialchars($_POST['note_'.$current], ENT_QUOTES, 'UTF-8') : $student['note'])?>'>
                    </td>
                </tr>
<?php
    }
?>
        <input type='hidden' name='lesson_id' value='<?=$course_data['lesson_id']?>'>
        <input type='hidden' name='end_lesson' value='<?=$course_data['lesson_end']?>'>
        <input type='hidden' name='student_number' value='<?=$current?>'>

    </table>

    <button class='button' type='submit' name='send_register'>Invia presenze</button>

</form>

<?php
} else {
    echo "<p><br><br><br>Il registro è disponibile solo durante le lezioni.</p>";
}
?>

    </main>

<?php 
    include "_footer.php"; 
?>