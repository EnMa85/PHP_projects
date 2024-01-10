<?php
include "_functions.php";

function connect(){
    $hostName = "localhost";
    $userName = "root";
    $password = "pyThon23@_";
    $databaseName = "registro";
    $conn = new mysqli($hostName, $userName, $password, $databaseName);
    if ($conn->connect_error) {
        die("Connection failed: ".$conn->connect_error);
    }
    return $conn;
}


// counts attempts of login
function count_attempt(){
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 1;
    } else {
        $_SESSION['login_attempts']++;
    }
    return null;
}


// checks the entered credentials and the state of the account
function check_login($user, $pw, &$teacher_data) {
    try {
        $conn = connect();
        $stmt = $conn->prepare("
                                SELECT utenti.password, utenti.id_docente, utenti.account_attivo,
                                       docenti.nome, docenti.cognome
                                FROM utenti
                                JOIN docenti ON utenti.id_docente = docenti.id
                                WHERE utenti.email = ?
                             ");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        // if email exists
        if ($result->num_rows > 0) {
            count_attempt();
            $row = $result->fetch_assoc();
            $active_account = $row['account_attivo'] == 1;
            $db_pw = $row['password'];

            if ($active_account && password_verify($pw, $db_pw)){
                $teacher = $row['nome']." ".$row['cognome'];
                $id_teacher = $row['id_docente'];
                $teacher_data = array('teacher' => $teacher, 'id_teacher' => $id_teacher);
                unset($_SESSION['login_attempts']);
                $stmt->close();
                $conn->close();
                return true;

            } else if (!$active_account) {
                unset($_SESSION['login_attempts']);
                throw new Exception("L'account è bloccato. Contatta l'assistenza.");

            } else if ($_SESSION['login_attempts'] >= 3) {
                unset($_SESSION['login_attempts']);
                block_account($user);
                throw new Exception("Hai effettuato troppi tentativi e l'account è stato bloccato.");

            } else {
                throw new Exception("Accesso non riuscito.");
            }
        } else {
            throw new Exception("Accesso non riuscito.");
        }  
    } catch (Exception $e) {

        // saves log error
        $error_message = $e->getMessage();
        log_error($error_message);

        $stmt->close();
        $conn->close();
        
        return $error_message;
    }
}


// updates an account as blocked
function block_account($user) {
    $conn = connect();
    $stmt = $conn->prepare("UPDATE utenti SET account_attivo = 0 WHERE email = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}


// reads teacher email from dabatase
function get_email($active_teacher){
    $conn = connect();
    $stmt = $conn->prepare("SELECT email FROM utenti WHERE id_docente = ?");
    $stmt->bind_param("i", $active_teacher);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // reads the first line of the result array
        $row = $result->fetch_assoc();
        $teacher_email = $row['email'];
    } else {
        $teacher_email = '';
    }
    $stmt->close();
    $conn->close();
    return $teacher_email;
 }


 // saves the result in an associative multidimensional array
 function save_result_by_row ($stmt){
    $result = $stmt->get_result();
    $array = array();
    while ($row = $result->fetch_assoc()) {
        $array[] = $row;
    }
    return $array;
}


// gets courses from database based on a conditional parameter
 function get_courses_by_period($active_teacher, $period) {
    $conn = connect();
    $stmt = $conn->prepare("SELECT 
                                corsi_per_docente.id_corso,
                                corsi.titolo,
                                categorie.nome AS categoria,
                                DATE_FORMAT(info_corsi.data_inizio, '%d-%m-%Y') AS data_inizio,
                                DATE_FORMAT(info_corsi.data_fine, '%d-%m-%Y') AS data_fine,
                                info_corsi.totale_ore
                                FROM corsi_per_docente
                                JOIN info_corsi ON info_corsi.id = corsi_per_docente.id_corso
                                JOIN corsi ON corsi.id = info_corsi.id_titolo
                                JOIN categorie ON corsi.id_categoria = categorie.id
                                WHERE corsi_per_docente.id_docente = ? AND $period
                            ");
    $stmt->bind_param("i", $active_teacher);
    $stmt->execute();
    $courses_by_teacher = save_result_by_row($stmt);
    $stmt->close();
    $conn->close();
    return $courses_by_teacher;
}

function active_courses($active_teacher) {
    $condition = "info_corsi.data_fine >= CURDATE() AND info_corsi.data_inizio <= CURDATE()";
    return get_courses_by_period($active_teacher, $condition);
}

function upcoming_courses($active_teacher) {
    $condition = "info_corsi.data_inizio > CURDATE()";
    return get_courses_by_period($active_teacher, $condition);
}

function course_history($active_teacher) {
    $condition = "info_corsi.data_fine < CURDATE()";
    return get_courses_by_period($active_teacher, $condition);
}


// checks if logged teacher has got programmed lessons
function next_lesson($active_teacher){
    $conn = connect();
    $stmt = $conn->prepare("SELECT
                                DATE_FORMAT(lezioni.giorno, '%d-%m-%Y') AS giorno, 
                                TIME_FORMAT(lezioni.ora_inizio, '%H:%i') AS ora_inizio,
                                TIME_FORMAT(lezioni.ora_fine, '%H:%i') AS ora_fine,
                                corsi.titolo,
                                lezioni.id, lezioni.id_corso
                                FROM lezioni_per_docente
                                JOIN lezioni ON lezioni.id = lezioni_per_docente.id_lezione AND giorno >= CURDATE() 
                                JOIN info_corsi ON info_corsi.id = lezioni.id_corso
                                JOIN corsi ON corsi.id = info_corsi.id_titolo
                                WHERE lezioni_per_docente.id_docente = ? 
                                ORDER BY STR_TO_DATE(giorno, '%Y-%m-%d') ASC, TIME_FORMAT(lezioni.ora_inizio, '%H:%i') ASC
                                LIMIT 1
                            ");
    $stmt->bind_param("i", $active_teacher);
    $stmt->execute();
    $next_lesson = save_result_by_row($stmt);
    $stmt->close();
    $conn->close();
    return $next_lesson;
 }


 // returns the calendar of all scheduled lessons
 function calendar($active_teacher){
    $conn = connect();
    $stmt = $conn->prepare("SELECT 
                                DATE_FORMAT(lezioni.giorno, '%d-%m-%Y') AS giorno, 
                                TIME_FORMAT(lezioni.ora_inizio, '%H:%i') AS ora_inizio,
                                TIME_FORMAT(lezioni.ora_fine, '%H:%i') AS ora_fine,
                                lezioni.id_corso,
                                corsi.titolo
                                FROM lezioni_per_docente
                                JOIN lezioni ON lezioni.id = lezioni_per_docente.id_lezione AND giorno >= CURDATE() 
                                JOIN info_corsi ON info_corsi.id = lezioni.id_corso
                                JOIN corsi ON corsi.id = info_corsi.id_titolo
                                WHERE lezioni_per_docente.id_docente = ? 
                                ORDER BY STR_TO_DATE(giorno, '%Y-%m-%d') ASC, TIME_FORMAT(lezioni.ora_inizio, '%H:%i') ASC
                           ");
    $stmt->bind_param("i", $active_teacher);
    $stmt->execute();
    $lessons_by_teacher = save_result_by_row($stmt);
    $stmt->close();
    $conn->close();
    return $lessons_by_teacher;
}


// returns the student list of a specific course
function student_list($course_id){
    $conn = connect();
    $stmt = $conn->prepare("SELECT corsisti.id, corsisti.cognome, corsisti.nome, corsisti.cf
                                FROM info_corsi
                                JOIN corsi_per_corsista ON corsi_per_corsista.id_corso = info_corsi.id
                                JOIN corsisti ON corsisti.id = corsi_per_corsista.id_corsista
                                WHERE info_corsi.id = ?
                                ORDER BY corsisti.cognome, corsisti.nome;
                           ");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $student_list = save_result_by_row($stmt);
    $stmt->close();
    $conn->close();
    return $student_list;
 }


// reads presence/absence states saved in a specific lesson
function lesson_status($active_lesson){
    $conn = connect();
    $stmt = $conn->prepare("SELECT * FROM stato_presenze WHERE id_lezione = ?");
    $stmt->bind_param("i", $active_lesson);
    $stmt->execute();
    $lesson_status = save_result_by_row($stmt);
    $stmt->close();
    $conn->close();
    return $lesson_status;
}


// reads the absences and relative data saved in the class register in a specific lesson
function lesson_register($active_lesson){
    $conn = connect();
    $stmt = $conn->prepare("SELECT * FROM registro_assenze WHERE id_lezione = ?");
    $stmt->bind_param("i", $active_lesson);
    $stmt->execute();
    $lesson_register = save_result_by_row($stmt);
    $stmt->close();
    $conn->close();
    return $lesson_register;
}
?>
